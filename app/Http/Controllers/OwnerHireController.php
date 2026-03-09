<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerUpdateHireStatusRequest;
use App\Models\ProjectHire;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerHireController extends Controller
{
    /** List the owner's hires with status filters and grouped hire counters. */
    public function showOwnerHires(Request $request): View
    {
        $ownerId = (int) $request->user()->id;
        $statusFilter = (string) $request->query('status', 'all');
        $allowedStatuses = ['all', 'active', 'completed', 'cancelled'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

        $hiresQuery = ProjectHire::query()
            ->where('owner_id', $ownerId)
            ->with([
                'project:id,title,reference_code,status,city,state,deadline',
                'contractor:id,first_name,last_name,email',
                'bid:id,project_id,quote_amount,proposed_timeline_days',
            ])
            ->latest('hired_at');

        if ($statusFilter !== 'all') {
            $hiresQuery->where('status', $statusFilter);
        }

        $hires = $hiresQuery->paginate(12)->withQueryString();
        $hireStats = $this->hireStatsForOwner($ownerId);

        return view('owner.hires.index', compact('hires', 'statusFilter', 'hireStats'));
    }

    /** Persist an owner-selected hire status and mirror it back to the linked project. */
    public function saveOwnerHireStatus(OwnerUpdateHireStatusRequest $request, ProjectHire $projectHire): RedirectResponse
    {
        abort_unless((int) $projectHire->owner_id === (int) $request->user()->id, 403);

        $nextStatus = (string) $request->validated()['status'];
        $projectHire->update(['status' => $nextStatus]);

        $projectStatus = match ($nextStatus) {
            'active' => 'in_progress',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            default => 'in_progress',
        };

        if ($projectHire->project) {
            $projectHire->project->update(['status' => $projectStatus]);
        }

        return back()->with('success', 'Hire status updated successfully.');
    }

    /**
     * Collapse hire filter counts into a single grouped query.
     *
     * @return array<string, int>
     */
    private function hireStatsForOwner(int $ownerId): array
    {
        $statusCounts = ProjectHire::query()
            ->where('owner_id', $ownerId)
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
