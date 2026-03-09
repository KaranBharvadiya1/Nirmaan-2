<?php

namespace App\Support;

use App\Models\User;
use RuntimeException;

class FirebaseCustomTokenFactory
{
    public function createTokenForUser(User $user): string
    {
        $clientEmail = (string) config('firebase.client_email', '');
        $privateKey = $this->normalizePrivateKey((string) config('firebase.private_key', ''));

        if ($clientEmail === '' || $privateKey === '') {
            throw new RuntimeException('Firebase server credentials are not configured.');
        }

        $now = time();
        $payload = [
            'iss' => $clientEmail,
            'sub' => $clientEmail,
            'aud' => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
            'iat' => $now,
            'exp' => $now + 3600,
            'uid' => $this->firebaseUid($user),
            'claims' => [
                'role' => $user->role,
                'laravel_user_id' => $user->id,
            ],
        ];

        return $this->signJwt($payload, $privateKey);
    }

    public function firebaseUid(User $user): string
    {
        return $this->firebaseUidFromRoleAndId((string) $user->role, (int) $user->id);
    }

    public function firebaseUidFromRoleAndId(string $role, int $userId): string
    {
        $sanitizedRole = strtolower((string) preg_replace('/[^a-z0-9]+/i', '_', $role));

        if ($sanitizedRole === '') {
            $sanitizedRole = 'user';
        }

        return "nirmaan_{$sanitizedRole}_{$userId}";
    }

    private function signJwt(array $payload, string $privateKey): string
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];

        $encodedHeader = $this->base64UrlEncode((string) json_encode($header, JSON_UNESCAPED_SLASHES));
        $encodedPayload = $this->base64UrlEncode((string) json_encode($payload, JSON_UNESCAPED_SLASHES));
        $unsignedToken = $encodedHeader.'.'.$encodedPayload;

        $privateKeyResource = openssl_pkey_get_private($privateKey);
        if ($privateKeyResource === false) {
            throw new RuntimeException('Invalid Firebase private key.');
        }

        $signature = '';
        $signed = openssl_sign($unsignedToken, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKeyResource);

        if (! $signed) {
            throw new RuntimeException('Failed to sign Firebase custom token.');
        }

        return $unsignedToken.'.'.$this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function normalizePrivateKey(string $privateKey): string
    {
        if ($privateKey === '') {
            return '';
        }

        return str_replace(["\r\n", "\r", '\n'], ["\n", "\n", "\n"], $privateKey);
    }
}

