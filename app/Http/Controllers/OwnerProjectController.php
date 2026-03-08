<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerStoreProjectRequest;
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

        $projectStats = [
            'all' => $owner->projects()->count(),
            'open' => $owner->projects()->where('status', 'open')->count(),
            'in_progress' => $owner->projects()->where('status', 'in_progress')->count(),
            'completed' => $owner->projects()->where('status', 'completed')->count(),
            'cancelled' => $owner->projects()->where('status', 'cancelled')->count(),
        ];

        return view('owner.projects.index', compact('projects', 'statusFilter', 'projectStats'));
    }

    public function showCreateProjectForm(): View
    {
        return view('owner.projects.create');
    }

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

    public function changeProjectStatus(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        $this->assertProjectOwnership($request, $project);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['open', 'reopen', 'in_progress', 'completed', 'cancelled'])],
        ]);

        $nextStatus = $validated['status'] === 'reopen' ? 'open' : $validated['status'];
        $project->update(['status' => $nextStatus]);

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

    public function showProjectDetails(Request $request, Project $project): View
    {
        $this->assertProjectOwnership($request, $project);

        $project->load([
            'projectDocuments',
            'bids' => function ($query): void {
                $query->latest('created_at');
            },
            'bids.contractor:id,first_name,last_name,email',
        ]);

        return view('owner.projects.show', compact('project'));
    }

    private function assertProjectOwnership(Request $request, Project $project): void
    {
        abort_unless((int) $project->owner_id === (int) $request->user()->id, 403);
    }

    private function isProjectEditable(Project $project): bool
    {
        return in_array($project->status, ['open', 'cancelled'], true);
    }

    private function isProjectDeletable(Project $project): bool
    {
        if (! in_array($project->status, ['open', 'cancelled'], true)) {
            return false;
        }

        return ! $project->bids()->exists();
    }

    /**
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

    private function generateProjectReferenceCode(): string
    {
        do {
            $code = 'NRM-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (Project::query()->where('reference_code', $code)->exists());

        return $code;
    }
}
