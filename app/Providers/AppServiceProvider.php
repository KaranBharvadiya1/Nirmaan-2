<?php

namespace App\Providers;

use App\Models\Bid;
use App\Support\FirebaseCustomTokenFactory;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services that do not require boot-time state.
     */
    public function register(): void
    {
        //
    }

    /**
     * Attach shared sidebar data and messaging config to the owner and contractor layouts.
     */
    public function boot(FirebaseCustomTokenFactory $tokenFactory): void
    {
        View::composer(['owner.layouts.app', 'contractor.layouts.app'], function ($view) use ($tokenFactory): void {
            $user = auth()->user();

            if (! $user) {
                $view->with('layoutNotificationCounts', ['bids' => 0]);
                $view->with('layoutMessagingBadgeConfig', $this->emptyMessagingBadgeConfig());

                return;
            }

            $unreadBids = $this->bidNotificationCountForUser($user);
            $view->with('layoutNotificationCounts', [
                'bids' => $unreadBids,
                'notifications' => $unreadBids,
            ]);
            $view->with('layoutMessagingBadgeConfig', $this->messagingBadgeConfigForUser($user, $tokenFactory));
        });
    }

    /**
     * Sidebar badges are loaded on every authenticated panel page, so keep the unread count query narrow.
     */
    private function bidNotificationCountForUser(User $user): int
    {
        if ($user->role === 'Owner') {
            return (int) Bid::query()
                ->join('projects', 'projects.id', '=', 'bids.project_id')
                ->where('projects.owner_id', $user->id)
                ->whereNull('bids.owner_viewed_at')
                ->count('bids.id');
        }

        if ($user->role === 'Contractor') {
            return (int) Bid::query()
                ->where('contractor_id', $user->id)
                ->whereIn('status', ['accepted', 'rejected'])
                ->whereNull('contractor_status_viewed_at')
                ->count();
        }

        return 0;
    }

    /** Build the Firebase client payload used by the shared messaging sidebar badge script. */
    private function messagingBadgeConfigForUser(User $user, FirebaseCustomTokenFactory $tokenFactory): array
    {
        return [
            'enabled' => (string) config('firebase.client_email', '') !== ''
                && (string) config('firebase.private_key', '') !== ''
                && (string) config('firebase.api_key', '') !== ''
                && (string) config('firebase.auth_domain', '') !== ''
                && (string) config('firebase.project_id', '') !== ''
                && (string) config('firebase.app_id', '') !== '',
            'firebaseClientConfig' => [
                'apiKey' => config('firebase.api_key'),
                'authDomain' => config('firebase.auth_domain'),
                'projectId' => config('firebase.project_id'),
                'storageBucket' => config('firebase.storage_bucket'),
                'messagingSenderId' => config('firebase.messaging_sender_id'),
                'appId' => config('firebase.app_id'),
                'measurementId' => config('firebase.measurement_id'),
            ],
            'firebaseTokenEndpoint' => route('firebase.custom_token'),
            'currentUserRole' => (string) $user->role,
            'currentUserFirebaseUid' => $tokenFactory->firebaseUid($user),
        ];
    }

    /** Return the disabled messaging badge payload used for guest requests. */
    private function emptyMessagingBadgeConfig(): array
    {
        return [
            'enabled' => false,
            'firebaseClientConfig' => [],
            'firebaseTokenEndpoint' => null,
            'currentUserRole' => null,
            'currentUserFirebaseUid' => null,
        ];
    }
}
