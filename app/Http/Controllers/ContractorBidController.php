<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractorStoreBidRequest;
use App\Models\Bid;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContractorBidController extends Controller
{
    public function showAvailableProjectsForBidding(Request $request): View
    {
        $contractorId = (int) $request->user()->id;

        $projects = Project::query()
            ->where('status', 'open')
            ->where('visibility', 'public')
            ->where('owner_id', '!=', $contractorId)
            ->with([
                'owner:id,first_name,last_name,email',
                'bids' => function ($query) use ($contractorId): void {
                    $query->where('contractor_id', $contractorId);
                },
            ])
            ->latest('created_at')
            ->paginate(12);

        return view('contractor.projects.index', compact('projects'));
    }

    public function showProjectBidForm(Request $request, Project $project): View
    {
        $contractorId = (int) $request->user()->id;

        abort_unless($project->status === 'open' && $project->visibility === 'public', 403);
        abort_if((int) $project->owner_id === $contractorId, 403);

        $existingBid = Bid::query()
            ->where('project_id', $project->id)
            ->where('contractor_id', $contractorId)
            ->first();

        $project->load('owner:id,first_name,last_name,email');

        return view('contractor.projects.show', compact('project', 'existingBid'));
    }

    public function submitProjectBid(ContractorStoreBidRequest $request, Project $project): RedirectResponse
    {
        $contractorId = (int) $request->user()->id;
        $validated = $request->validated();

        $existingBid = Bid::query()
            ->where('project_id', $project->id)
            ->where('contractor_id', $contractorId)
            ->first();

        if ($existingBid && $existingBid->status === 'accepted') {
            return back()->with('error', 'Accepted bid cannot be modified.');
        }

        $nextStatus = $existingBid?->status === 'rejected' ? 'pending' : ($existingBid?->status ?? 'pending');

        Bid::query()->updateOrCreate(
            [
                'project_id' => $project->id,
                'contractor_id' => $contractorId,
            ],
            [
                'quote_amount' => $validated['quote_amount'],
                'proposed_timeline_days' => $validated['proposed_timeline_days'] ?? null,
                'cover_message' => $validated['cover_message'] ?? null,
                'status' => $nextStatus,
            ],
        );

        return redirect()
            ->route('contractor.projects.show', $project)
            ->with('success', $existingBid ? 'Bid updated successfully.' : 'Bid submitted successfully.');
    }
}
