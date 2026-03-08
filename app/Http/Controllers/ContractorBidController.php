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

        $existingBid = Bid::query()
            ->where('project_id', $project->id)
            ->where('contractor_id', $contractorId)
            ->first();

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
            ],
        );

        return redirect()
            ->route('contractor.projects.show', $project)
            ->with('success', $existingBid ? 'Bid updated successfully.' : 'Bid submitted successfully.');
    }

    public function showMySubmittedBids(Request $request): View
    {
        $contractorId = (int) $request->user()->id;
        $statusFilter = (string) $request->query('status', 'all');
        $allowedStatuses = ['all', 'pending', 'shortlisted', 'accepted', 'rejected', 'withdrawn'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

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
        $bidStats = [
            'all' => $this->countContractorBids($contractorId),
            'pending' => $this->countContractorBids($contractorId, 'pending'),
            'shortlisted' => $this->countContractorBids($contractorId, 'shortlisted'),
            'accepted' => $this->countContractorBids($contractorId, 'accepted'),
            'rejected' => $this->countContractorBids($contractorId, 'rejected'),
            'withdrawn' => $this->countContractorBids($contractorId, 'withdrawn'),
        ];

        return view('contractor.bids.index', compact('bids', 'statusFilter', 'bidStats'));
    }

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

        $bid->update(['status' => 'withdrawn']);

        return back()->with('success', 'Bid withdrawn successfully.');
    }

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
        $hireStats = [
            'all' => $this->countContractorHires($contractorId),
            'active' => $this->countContractorHires($contractorId, 'active'),
            'completed' => $this->countContractorHires($contractorId, 'completed'),
            'cancelled' => $this->countContractorHires($contractorId, 'cancelled'),
        ];

        return view('contractor.awards.index', compact('hires', 'statusFilter', 'hireStats'));
    }

    private function countContractorBids(int $contractorId, ?string $status = null): int
    {
        $query = Bid::query()->where('contractor_id', $contractorId);

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->count();
    }

    private function countContractorHires(int $contractorId, ?string $status = null): int
    {
        $query = ProjectHire::query()->where('contractor_id', $contractorId);

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->count();
    }
}
