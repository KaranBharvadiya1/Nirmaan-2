<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\ProjectHire;
use App\Models\Shortlist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OwnerNotificationController extends Controller
{
    /** Display aggregated owner notifications for bids, hires, and shortlisted contractors. */
    public function index(Request $request): View
    {
        $ownerId = (int) $request->user()->id;
        $search = trim((string) $request->query('query', ''));

        $bidQuery = Bid::query()
            ->whereHas('project', function ($projectQuery) use ($ownerId): void {
                $projectQuery->where('owner_id', $ownerId);
            })
            ->with(['contractor:id,first_name,last_name,email', 'project:id,title,reference_code']);

        $hireQuery = ProjectHire::query()
            ->where('owner_id', $ownerId)
            ->with(['contractor:id,first_name,last_name,email', 'project:id,title']);

        $shortlistQuery = Shortlist::query()
            ->where('owner_id', $ownerId)
            ->with(['contractor:id,first_name,last_name,email']);

        if ($search !== '') {
            $lower = strtolower($search);
            $bidQuery->where(function ($query) use ($lower): void {
                $query->whereHas('contractor', function ($contractorQuery) use ($lower): void {
                    $contractorQuery->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%'.$lower.'%'])
                        ->orWhereRaw('LOWER(email) LIKE ?', ['%'.$lower.'%']);
                });
                $query->orWhereHas('project', function ($projectQuery) use ($lower): void {
                    $projectQuery->whereRaw('LOWER(title) LIKE ?', ['%'.$lower.'%'])
                        ->orWhereRaw('LOWER(reference_code) LIKE ?', ['%'.$lower.'%']);
                });
            });

            $hireQuery->where(function ($query) use ($lower): void {
                $query->whereHas('contractor', function ($contractorQuery) use ($lower): void {
                    $contractorQuery->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%'.$lower.'%'])
                        ->orWhereRaw('LOWER(email) LIKE ?', ['%'.$lower.'%']);
                });
                $query->orWhereHas('project', function ($projectQuery) use ($lower): void {
                    $projectQuery->whereRaw('LOWER(title) LIKE ?', ['%'.$lower.'%']);
                });
            });

            $shortlistQuery->where(function ($query) use ($lower): void {
                $query->whereHas('contractor', function ($contractorQuery) use ($lower): void {
                    $contractorQuery->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%'.$lower.'%'])
                        ->orWhereRaw('LOWER(email) LIKE ?', ['%'.$lower.'%']);
                });
                $query->orWhereRaw('LOWER(note) LIKE ?', ['%'.$lower.'%']);
            });
        }

        $cacheKey = sprintf('owner_notifications:%d:%s', $ownerId, md5($search));
        [$recentBids, $recentHires, $recentShortlists] = Cache::remember(
            $cacheKey,
            now()->addSeconds(30),
            function () use ($bidQuery, $hireQuery, $shortlistQuery) {
                $recentBids = $bidQuery
                    ->orderByDesc('created_at')
                    ->limit(6)
                    ->get();

                $recentHires = $hireQuery
                    ->orderByDesc('updated_at')
                    ->limit(4)
                    ->get();

                $recentShortlists = $shortlistQuery
                    ->orderByDesc('updated_at')
                    ->limit(4)
                    ->get();

                return [$recentBids, $recentHires, $recentShortlists];
            }
        );

        return view('owner.notifications.index', compact('recentBids', 'recentHires', 'recentShortlists', 'search'));
    }
}
