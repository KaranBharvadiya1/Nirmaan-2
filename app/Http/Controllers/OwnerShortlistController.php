<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerShortlistRequest;
use App\Models\Bid;
use App\Models\Shortlist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerShortlistController extends Controller
{
    /** Display the owner's curated shortlist with optional search/filter helpers. */
    public function index(Request $request): View
    {
        $ownerId = (int) $request->user()->id;
        $search = trim((string) $request->query('query', ''));

        $shortlists = Shortlist::with(['contractor', 'project', 'bid'])
            ->where('owner_id', $ownerId)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->whereHas('contractor', function ($contractorQuery) use ($search): void {
                        $contractorQuery->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%'.strtolower($search).'%'])
                            ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($search).'%']);
                    });
                    $inner->orWhereHas('project', function ($projectQuery) use ($search): void {
                        $projectQuery->whereRaw('LOWER(title) LIKE ?', ['%'.strtolower($search).'%'])
                            ->orWhereRaw('LOWER(reference_code) LIKE ?', ['%'.strtolower($search).'%']);
                    });
                });
            })
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('owner.shortlist.index', compact('shortlists', 'search'));
    }

    /** Persist a shortlist entry or refresh the existing one for the contractor. */
    public function store(OwnerShortlistRequest $request): RedirectResponse
    {
        $ownerId = (int) $request->user()->id;
        $validated = $request->validated();

        Shortlist::updateOrCreate(
            ['owner_id' => $ownerId, 'contractor_id' => $validated['contractor_id']],
            [
                'owner_id' => $ownerId,
                'contractor_id' => $validated['contractor_id'],
                'project_id' => $validated['project_id'] ?? null,
                'bid_id' => $validated['bid_id'] ?? null,
                'note' => $validated['note'] ?? null,
                'priority' => $validated['priority'] ?? 3,
                'status' => 'active',
            ],
        );

        if (! empty($validated['bid_id'])) {
            Bid::query()
                ->where('id', $validated['bid_id'])
                ->whereIn('status', ['pending'])
                ->update([
                    'status' => 'shortlisted',
                    'contractor_status_viewed_at' => null,
                ]);
        }

        return redirect()
            ->route('owner.shortlist.index')
            ->with('success', 'Contractor added to shortlist.');
    }

    /** Update the note or priority of an existing shortlist entry. */
    public function update(OwnerShortlistRequest $request, Shortlist $shortlist): RedirectResponse
    {
        abort_unless($shortlist->owner_id === (int) $request->user()->id, 403);

        $shortlist->update([
            'note' => $request->validated()['note'] ?? null,
            'priority' => $request->validated()['priority'] ?? 3,
        ]);

        return redirect()
            ->route('owner.shortlist.index')
            ->with('success', 'Shortlist entry updated.');
    }

    /** Remove a contractor from the owner's shortlist. */
    public function destroy(Request $request, Shortlist $shortlist): RedirectResponse
    {
        abort_unless((int) $shortlist->owner_id === (int) $request->user()->id, 403);
        $shortlist->delete();

        return back()->with('success', 'Contractor removed from shortlist.');
    }
}
