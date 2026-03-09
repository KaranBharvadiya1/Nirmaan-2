@php
    $portfolioWorkSample = $workSample ?? null;
    $existingExternalLinks = old(
        'external_video_links',
        $portfolioWorkSample
            ? $portfolioWorkSample->media->where('media_type', 'external_video')->pluck('external_url')->values()->all()
            : []
    );
@endphp

@if ($errors->any())
<div class="alert alert-danger shadow-sm mb-4">
    <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
    @csrf
    @if (($formMethod ?? 'POST') !== 'POST')
    @method($formMethod)
    @endif

    <div class="row g-3 g-md-4">
        <div class="col-12 col-xl-8">
            <div class="panel-card p-4 p-md-5">
                <h2 class="h5 fw-bold mb-3">Work Details</h2>
                <p class="helper-text mb-4">Add a completed project or work sample with enough context for owners to evaluate your quality and scope.</p>

                <div class="row g-3">
                    <div class="col-12">
                        <label for="title" class="form-label fw-semibold">Work Title</label>
                        <input id="title" name="title" type="text" value="{{ old('title', $portfolioWorkSample?->title) }}" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" placeholder="Example: Premium Villa Exterior and Interior Finishing" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="work_category" class="form-label fw-semibold">Category</label>
                        <input id="work_category" name="work_category" type="text" value="{{ old('work_category', $portfolioWorkSample?->work_category) }}" class="form-control{{ $errors->has('work_category') ? ' is-invalid' : '' }}" placeholder="Civil, Tiles, Plumbing, Interior">
                        @error('work_category')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="completed_year" class="form-label fw-semibold">Completed Year</label>
                        <input id="completed_year" name="completed_year" type="number" min="1990" max="{{ now()->addYear()->format('Y') }}" value="{{ old('completed_year', $portfolioWorkSample?->completed_year) }}" class="form-control{{ $errors->has('completed_year') ? ' is-invalid' : '' }}" placeholder="2025">
                        @error('completed_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="city" class="form-label fw-semibold">City</label>
                        <input id="city" name="city" type="text" value="{{ old('city', $portfolioWorkSample?->city) }}" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" placeholder="Ahmedabad">
                        @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="state" class="form-label fw-semibold">State</label>
                        <input id="state" name="state" type="text" value="{{ old('state', $portfolioWorkSample?->state) }}" class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}" placeholder="Gujarat">
                        @error('state')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea id="description" name="description" rows="6" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" placeholder="Describe the scope, material quality, execution challenges, and final output." required>{{ old('description', $portfolioWorkSample?->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="panel-card p-4 p-md-5">
                <h2 class="h5 fw-bold mb-3">Media Upload</h2>
                <p class="helper-text mb-4">Upload photos or short video files and optionally add YouTube or Vimeo links.</p>

                <div class="mb-3">
                    <label for="media_files" class="form-label fw-semibold">Images / Videos</label>
                    <input id="media_files" name="media_files[]" type="file" multiple class="form-control{{ $errors->has('media_files') || $errors->has('media_files.*') ? ' is-invalid' : '' }}" accept=".jpg,.jpeg,.png,.webp,.mp4,.mov,.webm">
                    @if($errors->has('media_files') || $errors->has('media_files.*'))
                    <div class="invalid-feedback d-block">
                        {{ $errors->first('media_files') ?: $errors->first('media_files.*') }}
                    </div>
                    @endif
                    <p class="helper-text mt-2 mb-0">Up to 8 files. Allowed: JPG, PNG, WEBP, MP4, MOV, WEBM. Max 50MB each.</p>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold">External Video Links</label>
                    @foreach (range(0, 2) as $videoIndex)
                    <input
                        name="external_video_links[]"
                        type="url"
                        value="{{ $existingExternalLinks[$videoIndex] ?? '' }}"
                        class="form-control{{ $errors->has('external_video_links.'.$videoIndex) ? ' is-invalid' : '' }} {{ $videoIndex < 2 ? 'mb-2' : '' }}"
                        placeholder="https://www.youtube.com/watch?v=..."
                    >
                    @if($errors->has('external_video_links.'.$videoIndex))
                    <div class="invalid-feedback d-block mb-2">{{ $errors->first('external_video_links.'.$videoIndex) }}</div>
                    @endif
                    @endforeach
                    <p class="helper-text mt-2 mb-0">Use this for hosted videos when you do not want to upload the file directly.</p>
                </div>
            </div>
        </div>
    </div>

    @if ($portfolioWorkSample && $portfolioWorkSample->media->isNotEmpty())
    <div class="panel-card p-4 p-md-5 mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Existing Media</h2>
                <p class="helper-text mb-0">Select any media you want removed when you save this update.</p>
            </div>
        </div>

        <div class="row g-3">
            @foreach ($portfolioWorkSample->media as $media)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="media-tile h-100">
                    <div class="media-preview">
                        @if ($media->media_type === 'image' && $media->file_url)
                        <img src="{{ $media->file_url }}" alt="{{ $media->original_name ?: $portfolioWorkSample->title }}" class="media-preview-image">
                        @elseif ($media->media_type === 'video' && $media->file_url)
                        <video class="media-preview-video" controls preload="metadata">
                            <source src="{{ $media->file_url }}" type="{{ $media->mime_type ?: 'video/mp4' }}">
                        </video>
                        @else
                        <div class="external-video-placeholder">
                            <i class="bi bi-play-btn fs-1 text-primary"></i>
                            <p class="small mb-0 text-secondary">External video link</p>
                        </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <p class="fw-semibold mb-1 small">{{ $media->original_name ?: 'External video' }}</p>
                            <p class="helper-text mb-0 small">
                                {{ ucfirst(str_replace('_', ' ', $media->media_type)) }}
                            </p>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{ $media->id }}" name="remove_media[]" id="remove_media_{{ $media->id }}">
                            <label class="form-check-label small" for="remove_media_{{ $media->id }}">Remove</label>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="d-flex flex-wrap gap-2 justify-content-between mt-4">
        <a href="{{ route('contractor.portfolio') }}" class="btn btn-outline-secondary px-4">Back</a>
        <button type="submit" class="btn btn-primary px-4">{{ $submitLabel }}</button>
    </div>
</form>
