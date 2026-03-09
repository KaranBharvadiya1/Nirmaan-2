<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractorWorkSampleRequest;
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
    public function showPortfolioIndex(Request $request): View
    {
        $contractor = $request->user();

        $workSamples = ContractorWorkSample::query()
            ->where('contractor_id', $contractor->id)
            ->with('media')
            ->latest('created_at')
            ->get();

        $portfolioStats = [
            'samples' => $workSamples->count(),
            'images' => $workSamples->sum(fn (ContractorWorkSample $sample): int => $sample->media->where('media_type', 'image')->count()),
            'videos' => $workSamples->sum(fn (ContractorWorkSample $sample): int => $sample->media->where('media_type', 'video')->count()),
            'external_videos' => $workSamples->sum(fn (ContractorWorkSample $sample): int => $sample->media->where('media_type', 'external_video')->count()),
        ];

        return view('contractor.portfolio.index', compact('workSamples', 'portfolioStats'));
    }

    public function showCreateForm(): View
    {
        return view('contractor.portfolio.create');
    }

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

    public function showEditForm(Request $request, ContractorWorkSample $workSample): View
    {
        $this->assertOwnership($request, $workSample);
        $workSample->load('media');

        return view('contractor.portfolio.edit', compact('workSample'));
    }

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

    public function deleteWork(Request $request, ContractorWorkSample $workSample): RedirectResponse
    {
        $this->assertOwnership($request, $workSample);
        $workSample->delete();

        return redirect()
            ->route('contractor.portfolio')
            ->with('success', 'Work sample deleted successfully.');
    }

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

        $portfolioStats = [
            'samples' => $workSamples->count(),
            'images' => $workSamples->sum(fn (ContractorWorkSample $sample): int => $sample->media->where('media_type', 'image')->count()),
            'videos' => $workSamples->sum(fn (ContractorWorkSample $sample): int => $sample->media->whereIn('media_type', ['video', 'external_video'])->count()),
        ];

        return view('contractors.show', compact('contractor', 'workSamples', 'portfolioStats'));
    }

    private function assertOwnership(Request $request, ContractorWorkSample $workSample): void
    {
        abort_unless((int) $workSample->contractor_id === (int) $request->user()->id, 403);
    }

    /**
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
