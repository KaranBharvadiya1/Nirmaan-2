<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractorStoreBidRequest;
use App\Models\Bid;
use App\Models\Project;
use App\Models\ProjectHire;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContractorBidController extends Controller
{
    /** List public projects a contractor can bid on and preload any bid already submitted by them. */
    public function showAvailableProjectsForBidding(Request $request): View
    {
        $contractorId = (int) $request->user()->id;

        $projects = Project::query()
            ->where('status', 'open')
            ->where('visibility', 'public')
            ->where('owner_id', '!=', $contractorId)
            ->whereDoesntHave('hire', function ($query): void {
                $query->where('status', 'active');
            })
            ->with([
                'owner:id,first_name,last_name,email',
                'bids' => function ($query) use ($contractorId): void {
                    $query->select('id', 'project_id', 'contractor_id', 'quote_amount', 'status')
                        ->where('contractor_id', $contractorId);
                },
            ])
            ->latest('created_at')
            ->paginate(12);

        return view('contractor.projects.index', compact('projects'));
    }

    /** Show the contractor-facing project detail and existing bid state for a single project. */
    public function showProjectBidForm(Request $request, Project $project): View
    {
        $contractorId = (int) $request->user()->id;

        $existingBid = Bid::query()
            ->where('project_id', $project->id)
            ->where('contractor_id', $contractorId)
            ->first();

        if ($existingBid && in_array($existingBid->status, ['accepted', 'rejected'], true) && ! $existingBid->contractor_status_viewed_at) {
            $existingBid->forceFill([
                'contractor_status_viewed_at' => now(),
            ])->save();

            $existingBid->refresh();
        }

        $project->load([
            'owner:id,first_name,last_name,email',
            'hire:id,project_id,owner_id,contractor_id,bid_id,status',
        ]);

        abort_if((int) $project->owner_id === $contractorId, 403);

        $isOpenForBidding = $project->status === 'open'
            && $project->visibility === 'public'
            && ! ($project->hire && $project->hire->status === 'active');

        $isAwardedToCurrentContractor = $project->hire
            && (int) $project->hire->contractor_id === $contractorId;

        abort_unless($isOpenForBidding || $existingBid || $isAwardedToCurrentContractor, 403);

        $canSubmitBid = $isOpenForBidding && (! $existingBid || $existingBid->status !== 'accepted');
        $viewContextNote = null;

        if (! $isOpenForBidding && $existingBid) {
            $viewContextNote = 'Bidding is closed for this project. You can view your submitted bid details.';
        } elseif ($isAwardedToCurrentContractor) {
            $viewContextNote = 'This project is awarded to you. Bid details are locked and shown as reference.';
        }

        return view('contractor.projects.show', compact(
            'project',
            'existingBid',
            'canSubmitBid',
            'viewContextNote',
        ));
    }

    /** Create or update the contractor's bid for the selected project. */
    public function submitProjectBid(ContractorStoreBidRequest $request, Project $project): RedirectResponse
    {
        $contractorId = (int) $request->user()->id;
        $validated = $request->validated();

        if ($project->status !== 'open' || $project->visibility !== 'public' || $project->hire()->where('status', 'active')->exists()) {
            return back()->with('error', 'Bidding is closed for this project.');
        }

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
                'owner_viewed_at' => null,
                'contractor_status_viewed_at' => now(),
            ],
        );

        return redirect()
            ->route('contractor.projects.show', $project)
            ->with('success', $existingBid ? 'Bid updated successfully.' : 'Bid submitted successfully.');
    }

    /** List the contractor's submitted bids and clear unread bid-decision notifications. */
    public function showMySubmittedBids(Request $request): View
    {
        $contractorId = (int) $request->user()->id;
        $statusFilter = (string) $request->query('status', 'all');
        $allowedStatuses = ['all', 'pending', 'shortlisted', 'accepted', 'rejected', 'withdrawn'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

        Bid::query()
            ->where('contractor_id', $contractorId)
            ->whereIn('status', ['accepted', 'rejected'])
            ->whereNull('contractor_status_viewed_at')
            ->update(['contractor_status_viewed_at' => now()]);

        $bidsQuery = Bid::query()
            ->where('contractor_id', $contractorId)
            ->with([
                'project:id,title,reference_code,status,owner_id,city,state,budget_min,budget_max,deadline',
                'project.owner:id,first_name,last_name,email',
                'project.hire:id,project_id,bid_id,status',
            ])
            ->latest('created_at');

        if ($statusFilter !== 'all') {
            $bidsQuery->where('status', $statusFilter);
        }

        $bids = $bidsQuery->paginate(12)->withQueryString();
        $bidStats = $this->bidStatsForContractor($contractorId);

        return view('contractor.bids.index', compact('bids', 'statusFilter', 'bidStats'));
    }

    /** Allow the contractor to withdraw a still-pending or shortlisted bid. */
    public function withdrawMyBid(Request $request, Bid $bid): RedirectResponse
    {
        $contractorId = (int) $request->user()->id;

        abort_unless((int) $bid->contractor_id === $contractorId, 403);

        $bid->loadMissing('project.hire');

        if (! in_array($bid->status, ['pending', 'shortlisted'], true)) {
            return back()->with('error', 'Only pending or shortlisted bids can be withdrawn.');
        }

        if ($bid->project->status !== 'open' || $bid->project->hire?->status === 'active') {
            return back()->with('error', 'Cannot withdraw bid after hiring starts.');
        }

        $bid->update([
            'status' => 'withdrawn',
            'owner_viewed_at' => null,
        ]);

        return back()->with('success', 'Bid withdrawn successfully.');
    }

    /** List projects awarded to the contractor with lightweight status counters. */
    public function showAwardedProjects(Request $request): View
    {
        $contractorId = (int) $request->user()->id;
        $statusFilter = (string) $request->query('status', 'all');
        $allowedStatuses = ['all', 'active', 'completed', 'cancelled'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

        $hiresQuery = ProjectHire::query()
            ->where('contractor_id', $contractorId)
            ->with([
                'project:id,title,reference_code,status,city,state,budget_currency,budget_min,budget_max,deadline',
                'owner:id,first_name,last_name,email',
                'bid:id,project_id,contractor_id,cover_message',
            ])
            ->latest('hired_at');

        if ($statusFilter !== 'all') {
            $hiresQuery->where('status', $statusFilter);
        }

        $hires = $hiresQuery->paginate(12)->withQueryString();
        $hireStats = $this->hireStatsForContractor($contractorId);

        return view('contractor.awards.index', compact('hires', 'statusFilter', 'hireStats'));
    }

    /**
     * Reuse grouped bid counts for the contractor bid filters instead of repeating one query per tab.
     *
     * @return array<string, int>
     */
    private function bidStatsForContractor(int $contractorId): array
    {
        $statusCounts = Bid::query()
            ->where('contractor_id', $contractorId)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->mapWithKeys(static fn ($count, $status): array => [(string) $status => (int) $count])
            ->all();

        return [
            'all' => array_sum($statusCounts),
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'shortlisted' => (int) ($statusCounts['shortlisted'] ?? 0),
            'accepted' => (int) ($statusCounts['accepted'] ?? 0),
            'rejected' => (int) ($statusCounts['rejected'] ?? 0),
            'withdrawn' => (int) ($statusCounts['withdrawn'] ?? 0),
        ];
    }

    /**
     * Awarded-project filters follow the same grouped-count pattern to keep the page query count stable.
     *
     * @return array<string, int>
     */
    private function hireStatsForContractor(int $contractorId): array
    {
        $statusCounts = ProjectHire::query()
            ->where('contractor_id', $contractorId)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->mapWithKeys(static fn ($count, $status): array => [(string) $status => (int) $count])
            ->all();

        return [
            'all' => array_sum($statusCounts),
            'active' => (int) ($statusCounts['active'] ?? 0),
            'completed' => (int) ($statusCounts['completed'] ?? 0),
            'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
        ];
    }
}
