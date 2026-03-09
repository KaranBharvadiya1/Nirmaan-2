<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\ContractorShortlist;
use App\Models\Project;
use App\Models\ProjectHire;
use App\Models\Shortlist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OwnerDashboardController extends Controller
{
    /** Build the owner dashboard KPI payload from grouped project, bid, hire, and shortlist data. */
    public function showDashboard(Request $request): View
    {
        $ownerId = (int) $request->user()->id;
        $tableAvailability = $this->dashboardTableAvailability();
        $projectCounts = $this->projectCountsForOwner($ownerId, $tableAvailability['projects']);

        $kpis = [
            'total_projects' => array_sum($projectCounts),
            'open_projects' => (int) ($projectCounts['open'] ?? 0),
            'in_progress_projects' => (int) (($projectCounts['in_progress'] ?? 0) + ($projectCounts['in-progress'] ?? 0)),
            'completed_projects' => (int) ($projectCounts['completed'] ?? 0),
            'bids_received' => $this->bidsReceivedCount($ownerId, $tableAvailability),
            'active_hires' => $this->activeHireCount($ownerId, $tableAvailability),
            'shortlist_count' => $this->shortlistCount($ownerId, $tableAvailability),
        ];

        return view('owner.dashboard', compact('kpis'));
    }

    /**
     * Keep the dashboard query budget flat by grouping project counts once.
     *
     * @return array<string, int>
     */
    private function projectCountsForOwner(int $ownerId, bool $projectsTableExists): array
    {
        if (! $projectsTableExists) {
            return [];
        }

        return Project::query()
            ->where('owner_id', $ownerId)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->mapWithKeys(static fn ($count, $status): array => [(string) $status => (int) $count])
            ->all();
    }

    /**
     * Schema checks are resolved once so the dashboard does not repeat them for every KPI.
     *
     * @return array<string, bool>
     */
    private function dashboardTableAvailability(): array
    {
        return [
            'projects' => Schema::hasTable('projects'),
            'bids' => Schema::hasTable('bids'),
            'project_hires' => Schema::hasTable('project_hires'),
            'contractor_shortlists' => Schema::hasTable('contractor_shortlists'),
            'shortlists' => Schema::hasTable('shortlists'),
        ];
    }

    /** Count every bid submitted against the owner's projects. */
    private function bidsReceivedCount(int $ownerId, array $tableAvailability): int
    {
        if (! $tableAvailability['projects'] || ! $tableAvailability['bids']) {
            return 0;
        }

        return (int) Bid::query()
            ->join('projects', 'projects.id', '=', 'bids.project_id')
            ->where('projects.owner_id', $ownerId)
            ->count('bids.id');
    }

    /** Count shortlisted contractors while supporting both shortlist table names used by the app. */
    private function shortlistCount(int $ownerId, array $tableAvailability): int
    {
        if ($tableAvailability['contractor_shortlists']) {
            return (int) ContractorShortlist::query()
                ->where('owner_id', $ownerId)
                ->count();
        }

        if ($tableAvailability['shortlists']) {
            return (int) Shortlist::query()
                ->where('owner_id', $ownerId)
                ->count();
        }

        return 0;
    }

    /** Count only currently active hire records for the owner dashboard. */
    private function activeHireCount(int $ownerId, array $tableAvailability): int
    {
        if (! $tableAvailability['project_hires']) {
            return 0;
        }

        return (int) ProjectHire::query()
            ->where('owner_id', $ownerId)
            ->where('status', 'active')
            ->count();
    }
}
