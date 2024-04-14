<?php

namespace LaravelOIDCAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use LaravelOIDCAuth\OIDCService;
use League\OAuth2\Client\Token\AccessToken;

class GetOIDCUserInfoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            /** @var AccessToken $token */
            $token = $request->user()->getAccessToken();

            $response = Http::timeout(5)
                ->withToken($token->getToken())
                ->get(config('oidc-auth.provider.urlResourceOwnerDetails'));

            if (! $response->ok()) {
                if ($response->unauthorized()) {
                    return redirect()->away(app(OIDCService::class)->buildAuthorizationUrl());
                }

                abort($response->status(), 'Something went wrong while get user info');
            }

            session(['user' => $response->json()]);
        }

        return $next($request);
    }
}
