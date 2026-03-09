<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerStoreProjectRequest;
use App\Models\Bid;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OwnerProjectController extends Controller
{
    /** List the owner's projects with lightweight filter stats and bid counts. */
    public function showProjects(Request $request): View
    {
        $owner = $request->user();
        $statusFilter = (string) $request->query('status', 'all');
        $allowedStatuses = ['all', 'open', 'in_progress', 'completed', 'cancelled'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

        $projectsQuery = Project::query()
            ->where('owner_id', $owner->id)
            ->withCount('bids')
            ->latest('created_at');

        if ($statusFilter !== 'all') {
            $projectsQuery->where('status', $statusFilter);
        }

        $projects = $projectsQuery->paginate(9)->withQueryString();
        $projectStats = $this->projectStatsForOwner((int) $owner->id);

        return view('owner.projects.index', compact('projects', 'statusFilter', 'projectStats'));
    }

    /** Render the owner form used to create a new construction project. */
    public function showCreateProjectForm(): View
    {
        return view('owner.projects.create');
    }

    /** Store a new project and any uploaded supporting documents for the owner. */
    public function saveProject(OwnerStoreProjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedDocuments = $request->file('project_documents', []);
        unset($validated['project_documents']);

        $project = Project::create([
            ...$validated,
            'owner_id' => $request->user()->id,
            'reference_code' => $this->generateProjectReferenceCode(),
        ]);

        $this->storeUploadedDocuments($project, $uploadedDocuments);

        return redirect()
            ->route('owner.projects.details', $project)
            ->with('success', 'Project posted successfully. Contractors can now discover it.')
            ->with('clearProjectDraft', true);
    }

    /** Open the edit screen for a project when the owner is allowed to modify it. */
    public function showProjectEditForm(Request $request, Project $project): View|RedirectResponse
    {
        $this->assertProjectOwnership($request, $project);

        if (! $this->isProjectEditable($project)) {
            return redirect()
                ->route('owner.projects.details', $project)
                ->with('error', 'Only open or cancelled projects can be edited.');
        }

        $project->load('projectDocuments');

        return view('owner.projects.edit', compact('project'));
    }

    /** Persist project field changes and any new supporting documents. */
    public function saveProjectChanges(OwnerStoreProjectRequest $request, Project $project): RedirectResponse
    {
        $this->assertProjectOwnership($request, $project);

        if (! $this->isProjectEditable($project)) {
            return redirect()
                ->route('owner.projects.details', $project)
                ->with('error', 'Only open or cancelled projects can be edited.');
        }

        $validated = $request->validated();
        $uploadedDocuments = $request->file('project_documents', []);
        unset($validated['project_documents']);

        $project->update($validated);
        $this->storeUploadedDocuments($project, $uploadedDocuments);

        return redirect()
            ->route('owner.projects.details', $project)
            ->with('success', 'Project updated successfully.');
    }

    /** Update the owner-controlled lifecycle status of a project and its linked hire. */
    public function changeProjectStatus(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        $this->assertProjectOwnership($request, $project);
        $project->loadMissing('hire');

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['open', 'reopen', 'in_progress', 'completed', 'cancelled'])],
        ]);

        $nextStatus = $validated['status'] === 'reopen' ? 'open' : $validated['status'];

        if ($project->hire?->status === 'active' && $nextStatus === 'open') {
            return $this->projectStatusErrorResponse($request, 'Cannot reopen a project with an active hired contractor.');
        }

        if ($project->hire?->status === 'completed' && $nextStatus !== 'completed') {
            return $this->projectStatusErrorResponse($request, 'Completed hired project cannot be moved back.');
        }

        if ($nextStatus === 'open' && $project->bids()->where('status', 'accepted')->exists()) {
            return $this->projectStatusErrorResponse($request, 'Project has an accepted bid and cannot be reopened.');
        }

        $project->update(['status' => $nextStatus]);

        if ($project->hire) {
            $hireStatus = match ($nextStatus) {
                'in_progress' => 'active',
                'completed' => 'completed',
                'cancelled' => 'cancelled',
                default => null,
            };

            if ($hireStatus !== null && $project->hire->status !== $hireStatus) {
                $project->hire->update(['status' => $hireStatus]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Project status updated successfully.',
                'status' => $nextStatus,
                'status_label' => str_replace('_', ' ', ucfirst($nextStatus)),
                'project_id' => $project->id,
            ]);
        }

        return redirect()
            ->route('owner.projects.details', $project)
            ->with('success', 'Project status updated successfully.');
    }

    /** Delete a project when it is still in a removable state and has no bids. */
    public function deleteProject(Request $request, Project $project): RedirectResponse
    {
        $this->assertProjectOwnership($request, $project);

        if (! $this->isProjectDeletable($project)) {
            return redirect()
                ->route('owner.projects.details', $project)
                ->with('error', 'Only open or cancelled projects without bids can be deleted.');
        }

        $project->delete();

        return redirect()
            ->route('owner.projects')
            ->with('success', 'Project deleted successfully.');
    }

    /** Show the full owner project detail view, including bids, documents, and hire summary. */
    public function showProjectDetails(Request $request, Project $project): View
    {
        $this->assertProjectOwnership($request, $project);

        Bid::query()
            ->where('project_id', $project->id)
            ->whereNull('owner_viewed_at')
            ->update(['owner_viewed_at' => now()]);

        $project->load([
            'projectDocuments',
            'hire.contractor:id,first_name,last_name,email',
            'hire.bid:id,project_id,contractor_id,quote_amount,proposed_timeline_days,cover_message',
            'bids' => function ($query): void {
                // The detail view does not need every bid column, so keep the eager load lean.
                $query->select('id', 'project_id', 'contractor_id', 'quote_amount', 'proposed_timeline_days', 'cover_message', 'status', 'created_at')
                    ->latest('created_at');
            },
            'bids.contractor:id,first_name,last_name,email',
        ]);

        return view('owner.projects.show', compact('project'));
    }

    /** Abort when a project does not belong to the authenticated owner. */
    private function assertProjectOwnership(Request $request, Project $project): void
    {
        abort_unless((int) $project->owner_id === (int) $request->user()->id, 403);
    }

    /**
     * Feed the filter chips from one grouped query instead of issuing one count per status.
     *
     * @return array<string, int>
     */
    private function projectStatsForOwner(int $ownerId): array
    {
        $statusCounts = Project::query()
            ->where('owner_id', $ownerId)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->mapWithKeys(static fn ($count, $status): array => [(string) $status => (int) $count])
            ->all();

        return [
            'all' => array_sum($statusCounts),
            'open' => (int) ($statusCounts['open'] ?? 0),
            'in_progress' => (int) ($statusCounts['in_progress'] ?? 0),
            'completed' => (int) ($statusCounts['completed'] ?? 0),
            'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
        ];
    }

    /** Check whether the current project status still allows edits. */
    private function isProjectEditable(Project $project): bool
    {
        return in_array($project->status, ['open', 'cancelled'], true);
    }

    /** Return a consistent validation error response for invalid status transitions. */
    private function projectStatusErrorResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->with('error', $message);
    }

    /** Check whether the current project can be deleted safely. */
    private function isProjectDeletable(Project $project): bool
    {
        if (! in_array($project->status, ['open', 'cancelled'], true)) {
            return false;
        }

        return ! $project->bids()->exists();
    }

    /**
     * Store uploaded project files on the public disk and link them back to the project.
     *
     * @param  array<int, UploadedFile>|array<empty>  $uploadedDocuments
     */
    private function storeUploadedDocuments(Project $project, array $uploadedDocuments): void
    {
        foreach ($uploadedDocuments as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $project->projectDocuments()->create([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $file->store('project-documents/'.$project->id, 'public'),
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

    /** Generate a short human-readable reference code that remains unique across projects. */
    private function generateProjectReferenceCode(): string
    {
        do {
            $code = 'NRM-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (Project::query()->where('reference_code', $code)->exists());

        return $code;
    }
}
