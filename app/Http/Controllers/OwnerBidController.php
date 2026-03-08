<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerUpdateBidStatusRequest;
use App\Models\Bid;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerBidController extends Controller
{
    public function showReceivedBids(Request $request): View
    {
        $ownerId = (int) $request->user()->id;
        $statusFilter = (string) $request->query('status', 'all');
        $allowedStatuses = ['all', 'pending', 'shortlisted', 'accepted', 'rejected', 'withdrawn'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

        $bidsQuery = Bid::query()
            ->whereHas('project', function ($query) use ($ownerId): void {
                $query->where('owner_id', $ownerId);
            })
            ->with([
                'project:id,title,reference_code,status,owner_id',
                'contractor:id,first_name,last_name,email',
            ])
            ->latest('created_at');

        if ($statusFilter !== 'all') {
            $bidsQuery->where('status', $statusFilter);
        }

        $bids = $bidsQuery->paginate(12)->withQueryString();

        $bidStats = [
            'all' => $this->countBidsForOwner($ownerId),
            'pending' => $this->countBidsForOwner($ownerId, 'pending'),
            'shortlisted' => $this->countBidsForOwner($ownerId, 'shortlisted'),
            'accepted' => $this->countBidsForOwner($ownerId, 'accepted'),
            'rejected' => $this->countBidsForOwner($ownerId, 'rejected'),
            'withdrawn' => $this->countBidsForOwner($ownerId, 'withdrawn'),
        ];

        return view('owner.bids.index', compact('bids', 'statusFilter', 'bidStats'));
    }

    public function changeBidStatus(OwnerUpdateBidStatusRequest $request, Bid $bid): JsonResponse|RedirectResponse
    {
        $bid->loadMissing('project');
        abort_unless((int) $bid->project->owner_id === (int) $request->user()->id, 403);

        $nextStatus = (string) $request->validated()['status'];
        $autoRejectedBidIds = [];

        if ($nextStatus === 'accepted') {
            $autoRejectedBidIds = Bid::query()
                ->where('project_id', $bid->project_id)
                ->where('id', '!=', $bid->id)
                ->whereIn('status', ['pending', 'shortlisted'])
                ->pluck('id')
                ->all();

            if ($autoRejectedBidIds !== []) {
                Bid::query()
                    ->whereIn('id', $autoRejectedBidIds)
                    ->update(['status' => 'rejected']);
            }

            if ($bid->project->status === 'open') {
                $bid->project->update(['status' => 'in_progress']);
            }
        }

        $bid->update(['status' => $nextStatus]);

        if ($request->expectsJson()) {
            $projectStatus = $bid->project->fresh()->status;

            return response()->json([
                'message' => 'Bid status updated successfully.',
                'status' => $bid->status,
                'status_label' => ucfirst($bid->status),
                'bid_id' => $bid->id,
                'project_id' => $bid->project_id,
                'project_status' => $projectStatus,
                'auto_rejected_bid_ids' => $autoRejectedBidIds,
            ]);
        }

        return back()->with('success', 'Bid status updated successfully.');
    }

    private function countBidsForOwner(int $ownerId, ?string $status = null): int
    {
        $query = Bid::query()
            ->whereHas('project', function ($projectQuery) use ($ownerId): void {
                $projectQuery->where('owner_id', $ownerId);
            });

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->count();
    }
}
