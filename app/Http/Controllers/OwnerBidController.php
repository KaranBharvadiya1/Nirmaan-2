<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerUpdateBidStatusRequest;
use App\Models\Bid;
use App\Models\ProjectHire;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        Bid::query()
            ->whereHas('project', function ($query) use ($ownerId): void {
                $query->where('owner_id', $ownerId);
            })
            ->whereNull('owner_viewed_at')
            ->update(['owner_viewed_at' => now()]);

        $bidsQuery = Bid::query()
            ->whereHas('project', function ($query) use ($ownerId): void {
                $query->where('owner_id', $ownerId);
            })
            ->with([
                'project:id,title,reference_code,status,owner_id',
                'project.hire:id,project_id,bid_id,status',
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
        $bid->loadMissing('project.hire');
        abort_unless((int) $bid->project->owner_id === (int) $request->user()->id, 403);

        $nextStatus = (string) $request->validated()['status'];
        $existingHire = $bid->project->hire;

        if ($bid->status === 'withdrawn') {
            return $this->bidStatusErrorResponse($request, 'Withdrawn bid cannot be updated.');
        }

        if ($bid->status === 'accepted' && $nextStatus !== 'accepted') {
            return $this->bidStatusErrorResponse($request, 'Accepted bid cannot be changed.');
        }

        if ($existingHire && (int) $existingHire->bid_id !== (int) $bid->id) {
            return $this->bidStatusErrorResponse($request, 'A contractor is already hired for this project.');
        }

        $autoRejectedBidIds = [];

        DB::transaction(function () use ($bid, $nextStatus, &$autoRejectedBidIds): void {
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
                        ->update([
                            'status' => 'rejected',
                            'contractor_status_viewed_at' => null,
                        ]);
                }

                ProjectHire::query()->updateOrCreate(
                    ['project_id' => $bid->project_id],
                    [
                        'owner_id' => $bid->project->owner_id,
                        'contractor_id' => $bid->contractor_id,
                        'bid_id' => $bid->id,
                        'agreed_amount' => $bid->quote_amount,
                        'agreed_timeline_days' => $bid->proposed_timeline_days,
                        'hired_at' => now(),
                        'status' => 'active',
                    ],
                );

                if ($bid->project->status !== 'in_progress') {
                    $bid->project->update(['status' => 'in_progress']);
                }
            }
            $bid->update([
                'status' => $nextStatus,
                'contractor_status_viewed_at' => in_array($nextStatus, ['accepted', 'rejected'], true)
                    ? null
                    : now(),
            ]);
        });

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

    private function bidStatusErrorResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->with('error', $message);
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
