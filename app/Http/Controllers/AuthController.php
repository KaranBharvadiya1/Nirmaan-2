<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;

class AuthController extends Controller
{
    public function showSignupForm(): RedirectResponse
    {
        return redirect()
            ->route('home')
            ->with('showSignup', true);
    }

    public function showLoginForm(): RedirectResponse
    {
        return redirect()
            ->route('home')
            ->with('showLogin', true);
    }

    public function signup(SignupRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'role' => $validated['role'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        if ($this->isApiRequest($request)) {
            $token = $this->createRoleToken($user);

            return $this->tokenResponse($user, $token, 201, 'Account created successfully.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'Account created successfully. You are now logged in.');
    }

    public function login(LoginRequest $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->validated();

        if ($this->isApiRequest($request)) {
            if (! Auth::validate($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials.',
                ], 401);
            }

            $user = User::query()
                ->where('email', $credentials['email'])
                ->firstOrFail();

            $token = $this->createRoleToken($user);

            return $this->tokenResponse($user, $token, 200, 'Logged in successfully.');
        }

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ], 'login')
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()
            ->intended(route('home'))
            ->with('success', 'Logged in successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role,
                'email' => $user->email,
            ],
            'abilities' => $user->currentAccessToken()?->abilities ?? [],
        ]);
    }

    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        if ($this->isApiRequest($request)) {
            $request->user()?->currentAccessToken()?->delete();

            return response()->json([
                'message' => 'Logged out successfully.',
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'You have been logged out.');
    }

    private function createRoleToken(User $user): NewAccessToken
    {
        return $user->createToken('auth_token', $this->abilitiesForRole($user->role));
    }

    /**
     * Assign ability set by selected role.
     *
     * @return array<int, string>
     */
    private function abilitiesForRole(string $role): array
    {
        return match ($role) {
            'Owner' => ['owner', 'projects:manage', 'bids:review', 'messages:send'],
            'Contractor' => ['contractor', 'bids:submit', 'messages:send'],
            default => ['user'],
        };
    }

    private function tokenResponse(User $user, NewAccessToken $token, int $status, string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'abilities' => $token->accessToken->abilities,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role,
                'email' => $user->email,
            ],
        ], $status);
    }

    private function isApiRequest(Request $request): bool
    {
        return $request->expectsJson() || $request->is('api/*');
    }
}
