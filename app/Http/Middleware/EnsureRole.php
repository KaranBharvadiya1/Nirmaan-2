<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Ensure the authenticated user belongs to one of the allowed application roles.
     *
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if ($roles === []) {
            return $next($request);
        }

        $allowedRoles = array_map(static fn (string $role): string => strtolower($role), $roles);

        if (! in_array(strtolower((string) $user->role), $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
