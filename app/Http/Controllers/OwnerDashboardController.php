<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\ContractorShortlist;
use App\Models\Project;
use App\Models\Shortlist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OwnerDashboardController extends Controller
{
    public function showDashboard(Request $request): View
    {
        $ownerId = (int) $request->user()->id;

        $kpis = [
            'total_projects' => $this->countProjectsByStatus($ownerId),
            'open_projects' => $this->countProjectsByStatus($ownerId, 'open'),
            'in_progress_projects' => $this->countInProgressProjects($ownerId),
            'completed_projects' => $this->countProjectsByStatus($ownerId, 'completed'),
            'bids_received' => $this->countBidsReceived($ownerId),
            'shortlist_count' => $this->countShortlistedContractors($ownerId),
        ];

        return view('owner.dashboard', compact('kpis'));
    }

    private function countProjectsByStatus(int $ownerId, ?string $status = null): int
    {
        if (! Schema::hasTable('projects')) {
            return 0;
        }

        $query = Project::query()
            ->where('owner_id', $ownerId);

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->count();
    }

    private function countInProgressProjects(int $ownerId): int
    {
        if (! Schema::hasTable('projects')) {
            return 0;
        }

        return Project::query()
            ->where('owner_id', $ownerId)
            ->whereIn('status', ['in_progress', 'in-progress'])
            ->count();
    }

    private function countBidsReceived(int $ownerId): int
    {
        if (! Schema::hasTable('projects') || ! Schema::hasTable('bids')) {
            return 0;
        }

        return Bid::query()
            ->whereHas('project', function ($query) use ($ownerId): void {
                $query->where('owner_id', $ownerId);
            })
            ->count();
    }

    private function countShortlistedContractors(int $ownerId): int
    {
        if (Schema::hasTable('contractor_shortlists')) {
            return ContractorShortlist::query()
                ->where('owner_id', $ownerId)
                ->count();
        }

        if (Schema::hasTable('shortlists')) {
            return Shortlist::query()
                ->where('owner_id', $ownerId)
                ->count();
        }

        return 0;
    }
}
