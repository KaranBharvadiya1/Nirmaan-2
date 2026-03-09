<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\ProjectHire;
use App\Models\User;
use App\Support\FirebaseCustomTokenFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    public function showOwnerMessages(Request $request, FirebaseCustomTokenFactory $tokenFactory): View
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'Owner', 403);

        return view('owner.messages.index', [
            'conversationContexts' => $this->buildOwnerConversationContexts((int) $user->id, $tokenFactory),
            'firebaseClientConfig' => $this->firebaseClientConfig(),
            'firebaseServerReady' => $this->firebaseServerReady(),
            'firebaseTokenEndpoint' => route('firebase.custom_token'),
            'currentUserMeta' => $this->buildCurrentUserMeta($user, $tokenFactory),
        ]);
    }

    public function showContractorMessages(Request $request, FirebaseCustomTokenFactory $tokenFactory): View
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'Contractor', 403);

        return view('contractor.messages.index', [
            'conversationContexts' => $this->buildContractorConversationContexts((int) $user->id, $tokenFactory),
            'firebaseClientConfig' => $this->firebaseClientConfig(),
            'firebaseServerReady' => $this->firebaseServerReady(),
            'firebaseTokenEndpoint' => route('firebase.custom_token'),
            'currentUserMeta' => $this->buildCurrentUserMeta($user, $tokenFactory),
        ]);
    }

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

    private function firebaseServerReady(): bool
    {
        return (string) config('firebase.client_email', '') !== ''
            && (string) config('firebase.private_key', '') !== '';
    }

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

    private function conversationId(int $projectId, int $ownerUserId, int $contractorUserId): string
    {
        return "project_{$projectId}_owner_{$ownerUserId}_contractor_{$contractorUserId}";
    }
}
