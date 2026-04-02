<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private array $supportedProviders = ['google', 'github'];

    /**
     * GET /api/auth/{provider}/redirect
     * Returns the OAuth provider authorization URL as JSON.
     * The Vue SPA redirects the browser to that URL.
     */
    public function redirect(string $provider): JsonResponse
    {
        $this->validateProvider($provider);

        $url = Socialite::driver($provider)
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    /**
     * GET /api/auth/{provider}/callback
     * Called by the OAuth provider after the user grants access.
     * Creates or finds the user, issues a Sanctum token,
     * then redirects the browser back to the Vue SPA callback page.
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Throwable $e) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect("{$frontendUrl}/oauth/callback?error=oauth_failed");
        }

        // Prevent duplicate accounts
        $user = User::where('provider', $provider)
                    ->where('provider_id', $socialUser->getId())
                    ->first();

        if (! $user) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'provider'    => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);
            } else {
                $user = User::create([
                    'name'        => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email'       => $socialUser->getEmail(),
                    'provider'    => $provider,
                    'provider_id' => $socialUser->getId(),
                    'password'    => null,
                ]);
            }
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        $userPayload = base64_encode(json_encode([
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'provider' => $user->provider,
            'is_oauth' => true,
        ]));

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        return redirect("{$frontendUrl}/oauth/callback?token={$token}&user={$userPayload}");
    }

    private function validateProvider(string $provider): void
    {
        if (! in_array($provider, $this->supportedProviders)) {
            abort(404, 'Provider not supported');
        }
    }
}