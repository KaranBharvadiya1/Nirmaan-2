<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\ProjectHire;
use App\Models\User;
use App\Support\FirebaseCustomTokenFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MessagingController extends Controller
{
    /** Render the owner messaging workspace with all eligible bid and hire conversations. */
    public function showOwnerMessages(Request $request, FirebaseCustomTokenFactory $tokenFactory): View
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'Owner', 403);

        return view('owner.messages.index', [
            'conversationContexts' => $this->buildConversationContextsForUser($user, $tokenFactory),
            'firebaseClientConfig' => $this->firebaseClientConfig(),
            'firebaseServerReady' => $this->firebaseServerReady(),
            'firebaseTokenEndpoint' => route('firebase.custom_token'),
            'chatAttachmentUploadEndpoint' => route('messages.attachments'),
            'currentUserMeta' => $this->buildCurrentUserMeta($user, $tokenFactory),
        ]);
    }

    /** Render the contractor messaging workspace with all eligible bid and hire conversations. */
    public function showContractorMessages(Request $request, FirebaseCustomTokenFactory $tokenFactory): View
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'Contractor', 403);

        return view('contractor.messages.index', [
            'conversationContexts' => $this->buildConversationContextsForUser($user, $tokenFactory),
            'firebaseClientConfig' => $this->firebaseClientConfig(),
            'firebaseServerReady' => $this->firebaseServerReady(),
            'firebaseTokenEndpoint' => route('firebase.custom_token'),
            'chatAttachmentUploadEndpoint' => route('messages.attachments'),
            'currentUserMeta' => $this->buildCurrentUserMeta($user, $tokenFactory),
        ]);
    }

    /** Upload chat media for a conversation after confirming that the user can access that thread. */
    public function uploadChatAttachments(Request $request, FirebaseCustomTokenFactory $tokenFactory): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['Owner', 'Contractor'], true), 403);

        $accessibleConversationIds = collect($this->buildConversationContextsForUser($user, $tokenFactory))
            ->pluck('conversation_id')
            ->values()
            ->all();

        $validated = $request->validate([
            'conversation_id' => ['required', 'string', Rule::in($accessibleConversationIds)],
            'attachments' => ['required', 'array', 'min:1', 'max:5'],
            'attachments.*' => [
                'required',
                'file',
                'max:25600',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime',
            ],
        ]);

        $storedAttachments = collect($request->file('attachments', []))
            ->filter(static fn ($file): bool => $file instanceof UploadedFile)
            ->map(function (UploadedFile $file) use ($validated): array {
                $mimeType = $this->chatAttachmentMimeType($file);
                $mediaType = $this->chatAttachmentMediaType($file, $mimeType);
                $storedPath = $file->store('chat-attachments/'.$validated['conversation_id'], 'public');

                return [
                    'media_type' => $mediaType,
                    'url' => Storage::url($storedPath),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $mimeType,
                    'file_size' => (int) $file->getSize(),
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'message' => 'Attachments uploaded successfully.',
            'attachments' => $storedAttachments,
        ]);
    }

    /** Issue a Firebase custom token that lets the current user join realtime chat safely. */
    public function issueFirebaseCustomToken(Request $request, FirebaseCustomTokenFactory $tokenFactory): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        if (! $this->firebaseServerReady()) {
            return response()->json([
                'message' => 'Firebase server credentials are missing. Add FIREBASE_CLIENT_EMAIL and FIREBASE_PRIVATE_KEY.',
            ], 422);
        }

        try {
            $token = $tokenFactory->createTokenForUser($user);
        } catch (\Throwable $throwable) {
            report($throwable);

            return response()->json([
                'message' => 'Failed to issue Firebase token. Check Firebase credentials.',
            ], 500);
        }

        return response()->json([
            'token' => $token,
            'uid' => $tokenFactory->firebaseUid($user),
            'expires_in' => 3600,
        ]);
    }

    /**
     * Build owner-visible messaging threads from project bids and awarded hires.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildOwnerConversationContexts(int $ownerId, FirebaseCustomTokenFactory $tokenFactory): array
    {
        $conversationMap = [];

        $projectBids = Bid::query()
            ->whereHas('project', function ($query) use ($ownerId): void {
                $query->where('owner_id', $ownerId);
            })
            ->with([
                'project:id,owner_id,title,reference_code,status',
                'contractor:id,first_name,last_name,email,role,profile_image_path',
            ])
            ->latest('created_at')
            ->get();

        foreach ($projectBids as $bid) {
            if (! $bid->project || ! $bid->contractor) {
                continue;
            }

            $context = $this->buildConversationContext(
                projectId: (int) $bid->project->id,
                projectTitle: (string) $bid->project->title,
                referenceCode: (string) $bid->project->reference_code,
                projectStatus: (string) $bid->project->status,
                ownerUserId: (int) $ownerId,
                contractorUserId: (int) $bid->contractor_id,
                counterparty: $bid->contractor,
                currentRole: 'Owner',
                relationshipType: 'bid',
                relationshipStatus: (string) $bid->status,
                sortEpoch: (int) ($bid->created_at?->timestamp ?? time()),
                tokenFactory: $tokenFactory,
                projectUrl: route('owner.projects.details', $bid->project),
            );

            $this->upsertConversationContext($conversationMap, $context);
        }

        $projectHires = ProjectHire::query()
            ->where('owner_id', $ownerId)
            ->with([
                'project:id,owner_id,title,reference_code,status',
                'contractor:id,first_name,last_name,email,role,profile_image_path',
            ])
            ->latest('hired_at')
            ->get();

        foreach ($projectHires as $projectHire) {
            if (! $projectHire->project || ! $projectHire->contractor) {
                continue;
            }

            $context = $this->buildConversationContext(
                projectId: (int) $projectHire->project_id,
                projectTitle: (string) $projectHire->project->title,
                referenceCode: (string) $projectHire->project->reference_code,
                projectStatus: (string) $projectHire->project->status,
                ownerUserId: (int) $ownerId,
                contractorUserId: (int) $projectHire->contractor_id,
                counterparty: $projectHire->contractor,
                currentRole: 'Owner',
                relationshipType: 'hire',
                relationshipStatus: (string) $projectHire->status,
                sortEpoch: (int) ($projectHire->hired_at?->timestamp ?? time()),
                tokenFactory: $tokenFactory,
                projectUrl: route('owner.projects.details', $projectHire->project),
            );

            $this->upsertConversationContext($conversationMap, $context);
        }

        return $this->sortedConversationContexts($conversationMap);
    }

    /**
     * Build contractor-visible messaging threads from submitted bids and awarded projects.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildContractorConversationContexts(int $contractorId, FirebaseCustomTokenFactory $tokenFactory): array
    {
        $conversationMap = [];

        $submittedBids = Bid::query()
            ->where('contractor_id', $contractorId)
            ->with([
                'project:id,owner_id,title,reference_code,status',
                'project.owner:id,first_name,last_name,email,role,profile_image_path',
            ])
            ->latest('created_at')
            ->get();

        foreach ($submittedBids as $bid) {
            if (! $bid->project || ! $bid->project->owner) {
                continue;
            }

            $context = $this->buildConversationContext(
                projectId: (int) $bid->project->id,
                projectTitle: (string) $bid->project->title,
                referenceCode: (string) $bid->project->reference_code,
                projectStatus: (string) $bid->project->status,
                ownerUserId: (int) $bid->project->owner_id,
                contractorUserId: (int) $contractorId,
                counterparty: $bid->project->owner,
                currentRole: 'Contractor',
                relationshipType: 'bid',
                relationshipStatus: (string) $bid->status,
                sortEpoch: (int) ($bid->created_at?->timestamp ?? time()),
                tokenFactory: $tokenFactory,
                projectUrl: route('contractor.projects.show', $bid->project),
            );

            $this->upsertConversationContext($conversationMap, $context);
        }

        $awardedProjects = ProjectHire::query()
            ->where('contractor_id', $contractorId)
            ->with([
                'project:id,owner_id,title,reference_code,status',
                'owner:id,first_name,last_name,email,role,profile_image_path',
            ])
            ->latest('hired_at')
            ->get();

        foreach ($awardedProjects as $projectHire) {
            if (! $projectHire->project || ! $projectHire->owner) {
                continue;
            }

            $context = $this->buildConversationContext(
                projectId: (int) $projectHire->project_id,
                projectTitle: (string) $projectHire->project->title,
                referenceCode: (string) $projectHire->project->reference_code,
                projectStatus: (string) $projectHire->project->status,
                ownerUserId: (int) $projectHire->owner_id,
                contractorUserId: (int) $contractorId,
                counterparty: $projectHire->owner,
                currentRole: 'Contractor',
                relationshipType: 'hire',
                relationshipStatus: (string) $projectHire->status,
                sortEpoch: (int) ($projectHire->hired_at?->timestamp ?? time()),
                tokenFactory: $tokenFactory,
                projectUrl: route('contractor.projects.show', $projectHire->project),
            );

            $this->upsertConversationContext($conversationMap, $context);
        }

        return $this->sortedConversationContexts($conversationMap);
    }

    /**
     * Resolve the correct conversation builder for the current authenticated role.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildConversationContextsForUser(User $user, FirebaseCustomTokenFactory $tokenFactory): array
    {
        return match ((string) $user->role) {
            'Owner' => $this->buildOwnerConversationContexts((int) $user->id, $tokenFactory),
            'Contractor' => $this->buildContractorConversationContexts((int) $user->id, $tokenFactory),
            default => [],
        };
    }

    /**
     * Merge conversation contexts so a bid thread can later upgrade to a hire thread cleanly.
     *
     * @param  array<string, array<string, mixed>>  $conversationMap
     * @param  array<string, mixed>  $context
     */
    private function upsertConversationContext(array &$conversationMap, array $context): void
    {
        $conversationId = (string) $context['conversation_id'];

        if (! isset($conversationMap[$conversationId])) {
            $conversationMap[$conversationId] = $context;

            return;
        }

        $existing = $conversationMap[$conversationId];

        $existingEpoch = (int) ($existing['sort_epoch'] ?? 0);
        $incomingEpoch = (int) ($context['sort_epoch'] ?? 0);

        if ($incomingEpoch > $existingEpoch) {
            $existing['sort_epoch'] = $incomingEpoch;
            $existing['project']['status'] = $context['project']['status'] ?? $existing['project']['status'];
            $existing['relationship']['status'] = $context['relationship']['status'] ?? $existing['relationship']['status'];
        }

        $existingType = (string) ($existing['relationship']['type'] ?? 'bid');
        $incomingType = (string) ($context['relationship']['type'] ?? 'bid');

        if ($existingType !== 'hire' && $incomingType === 'hire') {
            $existing['relationship'] = $context['relationship'];
        }

        $conversationMap[$conversationId] = $existing;
    }

    /**
     * Return conversation contexts sorted from newest activity to oldest.
     *
     * @param  array<string, array<string, mixed>>  $conversationMap
     * @return array<int, array<string, mixed>>
     */
    private function sortedConversationContexts(array $conversationMap): array
    {
        $contexts = array_values($conversationMap);

        usort($contexts, function (array $left, array $right): int {
            return ((int) ($right['sort_epoch'] ?? 0)) <=> ((int) ($left['sort_epoch'] ?? 0));
        });

        return $contexts;
    }

    /**
     * Package the shared conversation metadata used by the realtime chat UI.
     *
     * @return array<string, mixed>
     */
    private function buildConversationContext(
        int $projectId,
        string $projectTitle,
        string $referenceCode,
        string $projectStatus,
        int $ownerUserId,
        int $contractorUserId,
        User $counterparty,
        string $currentRole,
        string $relationshipType,
        string $relationshipStatus,
        int $sortEpoch,
        FirebaseCustomTokenFactory $tokenFactory,
        string $projectUrl,
    ): array {
        $conversationId = $this->conversationId($projectId, $ownerUserId, $contractorUserId);

        return [
            'conversation_id' => $conversationId,
            'project' => [
                'id' => $projectId,
                'title' => $projectTitle,
                'reference_code' => $referenceCode,
                'status' => $projectStatus,
                'url' => $projectUrl,
            ],
            'counterparty' => [
                'id' => (int) $counterparty->id,
                'name' => $this->displayName($counterparty),
                'email' => (string) $counterparty->email,
                'role' => (string) $counterparty->role,
                'profile_image_url' => $counterparty->profile_image_url,
            ],
            'participants' => [
                'owner_user_id' => $ownerUserId,
                'contractor_user_id' => $contractorUserId,
                'owner_uid' => $tokenFactory->firebaseUidFromRoleAndId('Owner', $ownerUserId),
                'contractor_uid' => $tokenFactory->firebaseUidFromRoleAndId('Contractor', $contractorUserId),
            ],
            'relationship' => [
                'type' => $relationshipType,
                'status' => $relationshipStatus,
            ],
            'current_role' => $currentRole,
            'sort_epoch' => $sortEpoch,
        ];
    }

    /**
     * Return the current Laravel user's identity payload expected by the messaging frontend.
     *
     * @return array<string, mixed>
     */
    private function buildCurrentUserMeta(User $user, FirebaseCustomTokenFactory $tokenFactory): array
    {
        return [
            'user_id' => (int) $user->id,
            'name' => $this->displayName($user),
            'email' => (string) $user->email,
            'role' => (string) $user->role,
            'firebase_uid' => $tokenFactory->firebaseUid($user),
        ];
    }

    /**
     * Expose the Firebase client configuration needed by the browser SDK.
     *
     * @return array<string, string|null>
     */
    private function firebaseClientConfig(): array
    {
        return [
            'apiKey' => config('firebase.api_key'),
            'authDomain' => config('firebase.auth_domain'),
            'projectId' => config('firebase.project_id'),
            'storageBucket' => config('firebase.storage_bucket'),
            'messagingSenderId' => config('firebase.messaging_sender_id'),
            'appId' => config('firebase.app_id'),
            'measurementId' => config('firebase.measurement_id'),
        ];
    }

    /** Check whether the server-side Firebase service-account credentials are available. */
    private function firebaseServerReady(): bool
    {
        return (string) config('firebase.client_email', '') !== ''
            && (string) config('firebase.private_key', '') !== '';
    }

    /** Build a stable display name for the UI from first name, name, or email fallback. */
    private function displayName(User $user): string
    {
        $fullName = trim((string) $user->first_name.' '.(string) $user->last_name);

        if ($fullName !== '') {
            return $fullName;
        }

        if ((string) $user->name !== '') {
            return (string) $user->name;
        }

        return (string) $user->email;
    }

    /** Normalize uploaded chat media MIME types when browsers send a generic octet-stream value. */
    private function chatAttachmentMimeType(UploadedFile $file): string
    {
        $detectedMimeType = (string) ($file->getMimeType() ?: $file->getClientMimeType());
        if ($detectedMimeType !== '' && $detectedMimeType !== 'application/octet-stream') {
            return $detectedMimeType;
        }

        return match (strtolower((string) $file->getClientOriginalExtension())) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            default => $detectedMimeType ?: 'application/octet-stream',
        };
    }

    /** Classify chat uploads into image or video buckets for frontend rendering. */
    private function chatAttachmentMediaType(UploadedFile $file, string $mimeType): string
    {
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return in_array(strtolower((string) $file->getClientOriginalExtension()), ['mp4', 'webm', 'mov'], true)
            ? 'video'
            : 'image';
    }

    /** Generate a deterministic conversation id from the project and participant ids. */
    private function conversationId(int $projectId, int $ownerUserId, int $contractorUserId): string
    {
        return "project_{$projectId}_owner_{$ownerUserId}_contractor_{$contractorUserId}";
    }
}
