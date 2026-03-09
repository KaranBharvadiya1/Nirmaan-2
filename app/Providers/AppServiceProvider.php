<?php

namespace App\Providers;

use App\Models\Bid;
use App\Support\FirebaseCustomTokenFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(FirebaseCustomTokenFactory $tokenFactory): void
    {
        View::composer(['owner.layouts.app', 'contractor.layouts.app'], function ($view) use ($tokenFactory): void {
            $user = auth()->user();

            $notificationCounts = [
                'bids' => 0,
            ];

            $messagingBadgeConfig = [
                'enabled' => false,
                'firebaseClientConfig' => [],
                'firebaseTokenEndpoint' => null,
                'currentUserRole' => null,
                'currentUserFirebaseUid' => null,
            ];

            if (! $user) {
                $view->with('layoutNotificationCounts', $notificationCounts);
                $view->with('layoutMessagingBadgeConfig', $messagingBadgeConfig);

                return;
            }

            if ($user->role === 'Owner') {
                $notificationCounts['bids'] = Bid::query()
                    ->whereHas('project', function ($query) use ($user): void {
                        $query->where('owner_id', $user->id);
                    })
                    ->whereNull('owner_viewed_at')
                    ->count();
            }

            if ($user->role === 'Contractor') {
                $notificationCounts['bids'] = Bid::query()
                    ->where('contractor_id', $user->id)
                    ->whereIn('status', ['accepted', 'rejected'])
                    ->whereNull('contractor_status_viewed_at')
                    ->count();
            }

            $messagingBadgeConfig = [
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

            $view->with('layoutNotificationCounts', $notificationCounts);
            $view->with('layoutMessagingBadgeConfig', $messagingBadgeConfig);
        });
    }
}
