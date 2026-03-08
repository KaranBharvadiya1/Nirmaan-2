<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerUpdateHireStatusRequest;
use App\Models\ProjectHire;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerHireController extends Controller
{
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
        $hireStats = [
            'all' => $this->countOwnerHires($ownerId),
            'active' => $this->countOwnerHires($ownerId, 'active'),
            'completed' => $this->countOwnerHires($ownerId, 'completed'),
            'cancelled' => $this->countOwnerHires($ownerId, 'cancelled'),
        ];

        return view('owner.hires.index', compact('hires', 'statusFilter', 'hireStats'));
    }

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

    private function countOwnerHires(int $ownerId, ?string $status = null): int
    {
        $query = ProjectHire::query()->where('owner_id', $ownerId);

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->count();
    }
}
