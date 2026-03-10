<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractorWorkSampleRequest;
use App\Models\Bid;
use App\Models\ContractorWorkMedia;
use App\Models\ContractorWorkSample;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ContractorPortfolioController extends Controller
{
    /** Show the contractor portfolio dashboard with uploaded work samples and media stats. */
    public function showPortfolioIndex(Request $request): View
    {
        $contractor = $request->user();

        $workSamples = ContractorWorkSample::query()
            ->where('contractor_id', $contractor->id)
            ->with('media')
            ->latest('created_at')
            ->get();

        $portfolioStats = $this->portfolioStats($workSamples);

        return view('contractor.portfolio.index', compact('workSamples', 'portfolioStats'));
    }

    /** Render the contractor form for creating a new portfolio work sample. */
    public function showCreateForm(): View
    {
        return view('contractor.portfolio.create');
    }

    /** Store a new contractor work sample together with its uploaded or external media. */
    public function saveWork(ContractorWorkSampleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedMedia = $request->file('media_files', []);
        $externalVideoLinks = $validated['external_video_links'] ?? [];

        unset($validated['media_files'], $validated['external_video_links'], $validated['remove_media']);

        $workSample = ContractorWorkSample::query()->create([
            ...$validated,
            'contractor_id' => $request->user()->id,
        ]);

        $this->storeMedia($workSample, $uploadedMedia, $externalVideoLinks);

        return redirect()
            ->route('contractor.portfolio')
            ->with('success', 'Work sample added successfully.');
    }

    /** Open the edit form for a contractor-owned work sample. */
    public function showEditForm(Request $request, ContractorWorkSample $workSample): View
    {
        $this->assertOwnership($request, $workSample);
        $workSample->load('media');

        return view('contractor.portfolio.edit', compact('workSample'));
    }

    /** Save edits to an existing work sample, including removals and new media uploads. */
    public function saveWorkChanges(ContractorWorkSampleRequest $request, ContractorWorkSample $workSample): RedirectResponse
    {
        $this->assertOwnership($request, $workSample);

        $validated = $request->validated();
        $uploadedMedia = $request->file('media_files', []);
        $externalVideoLinks = $validated['external_video_links'] ?? [];
        $removeMediaIds = collect((array) ($validated['remove_media'] ?? []))
            ->map(static fn ($id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->values()
            ->all();

        unset($validated['media_files'], $validated['external_video_links'], $validated['remove_media']);

        $workSample->update($validated);

        if ($removeMediaIds !== []) {
            $workSample->media()
                ->whereIn('id', $removeMediaIds)
                ->get()
                ->each(function (ContractorWorkMedia $media): void {
                    if ($media->file_path) {
                        Storage::disk('public')->delete($media->file_path);
                    }

                    $media->delete();
                });
        }

        $this->storeMedia($workSample, $uploadedMedia, $externalVideoLinks);

        return redirect()
            ->route('contractor.portfolio')
            ->with('success', 'Work sample updated successfully.');
    }

    /** Delete a contractor work sample and all linked media. */
    public function deleteWork(Request $request, ContractorWorkSample $workSample): RedirectResponse
    {
        $this->assertOwnership($request, $workSample);
        $workSample->delete();

        return redirect()
            ->route('contractor.portfolio')
            ->with('success', 'Work sample deleted successfully.');
    }

    /** Render the owner- or contractor-facing public portfolio page for a contractor. */
    public function showPublicPortfolio(Request $request, User $contractor): View
    {
        abort_unless($contractor->role === 'Contractor', 404);

        $viewer = $request->user();
        abort_unless($viewer && in_array($viewer->role, ['Owner', 'Contractor'], true), 403);

        $workSamples = ContractorWorkSample::query()
            ->where('contractor_id', $contractor->id)
            ->with('media')
            ->latest('created_at')
            ->get();

        $portfolioStats = $this->publicPortfolioStats($workSamples);
        $viewerId = $viewer?->id;
        $viewerIsOwner = $viewer?->role === 'Owner';
        $ownerBidStats = [
            'pending' => 0,
            'shortlisted' => 0,
            'accepted' => 0,
            'rejected' => 0,
            'withdrawn' => 0,
            'all' => 0,
        ];
        $ownerBids = collect();

        if ($viewerIsOwner) {
            $statusCounts = Bid::query()
                ->join('projects', 'projects.id', '=', 'bids.project_id')
                ->where('projects.owner_id', $viewerId)
                ->where('bids.contractor_id', $contractor->id)
                ->selectRaw('bids.status as status_key, COUNT(bids.id) as aggregate')
                ->groupBy('bids.status')
                ->pluck('aggregate', 'status_key')
                ->mapWithKeys(static fn ($count, $status): array => [(string) $status => (int) $count])
                ->all();

            $ownerBidStats = [
                'pending' => (int) ($statusCounts['pending'] ?? 0),
                'shortlisted' => (int) ($statusCounts['shortlisted'] ?? 0),
                'accepted' => (int) ($statusCounts['accepted'] ?? 0),
                'rejected' => (int) ($statusCounts['rejected'] ?? 0),
                'withdrawn' => (int) ($statusCounts['withdrawn'] ?? 0),
            ];
            $ownerBidStats['all'] = array_sum($ownerBidStats);

            $ownerBids = Bid::query()
                ->where('contractor_id', $contractor->id)
                ->whereHas('project', function ($query) use ($viewerId): void {
                    $query->where('owner_id', $viewerId);
                })
                ->with(['project:id,title,reference_code', 'project.hire'])
                ->latest('created_at')
                ->limit(5)
                ->get();
        }

        return view('contractors.show', compact('contractor', 'workSamples', 'portfolioStats', 'ownerBids', 'ownerBidStats', 'viewerIsOwner'));
    }

    /** Abort when a work sample does not belong to the authenticated contractor. */
    private function assertOwnership(Request $request, ContractorWorkSample $workSample): void
    {
        abort_unless((int) $workSample->contractor_id === (int) $request->user()->id, 403);
    }

    /**
     * The portfolio page already has the media collections in memory, so stats are derived from one flattened pass.
     *
     * @param  \Illuminate\Support\Collection<int, ContractorWorkSample>  $workSamples
     * @return array<string, int>
     */
    private function portfolioStats($workSamples): array
    {
        $mediaCounts = $workSamples
            ->pluck('media')
            ->flatten(1)
            ->countBy('media_type');

        return [
            'samples' => $workSamples->count(),
            'images' => (int) ($mediaCounts['image'] ?? 0),
            'videos' => (int) ($mediaCounts['video'] ?? 0),
            'external_videos' => (int) ($mediaCounts['external_video'] ?? 0),
        ];
    }

    /**
     * Public portfolios show uploaded and external videos in the same headline metric.
     *
     * @param  \Illuminate\Support\Collection<int, ContractorWorkSample>  $workSamples
     * @return array<string, int>
     */
    private function publicPortfolioStats($workSamples): array
    {
        $portfolioStats = $this->portfolioStats($workSamples);

        return [
            'samples' => $portfolioStats['samples'],
            'images' => $portfolioStats['images'],
            'videos' => $portfolioStats['videos'] + $portfolioStats['external_videos'],
        ];
    }

    /**
     * Store newly uploaded portfolio media and external video links for a work sample.
     *
     * @param  array<int, UploadedFile>|array<empty>  $uploadedMedia
     * @param  array<int, string>  $externalVideoLinks
     */
    private function storeMedia(ContractorWorkSample $workSample, array $uploadedMedia, array $externalVideoLinks): void
    {
        $sortOrder = (int) ($workSample->media()->max('sort_order') ?? -1) + 1;

        foreach ($uploadedMedia as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $mimeType = (string) $file->getClientMimeType();
            $mediaType = str_starts_with($mimeType, 'video/') ? 'video' : 'image';

            $workSample->media()->create([
                'media_type' => $mediaType,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $file->store('contractor-portfolio/'.$workSample->id, 'public'),
                'mime_type' => $mimeType,
                'file_size' => $file->getSize(),
                'sort_order' => $sortOrder++,
            ]);
        }

        foreach ($externalVideoLinks as $link) {
            $trimmedLink = trim((string) $link);

            if ($trimmedLink === '') {
                continue;
            }

            $workSample->media()->create([
                'media_type' => 'external_video',
                'external_url' => $trimmedLink,
                'sort_order' => $sortOrder++,
            ]);
        }
    }
}
