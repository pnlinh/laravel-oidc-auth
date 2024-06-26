<?php

namespace LaravelOIDCAuth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use LaravelOIDCAuth\Exceptions\AuthenticationErrorException;
use LaravelOIDCAuth\Exceptions\AuthenticationException;
use LaravelOIDCAuth\Exceptions\InvalidStateException;
use LaravelOIDCAuth\OIDCService;

class CallbackController extends Controller
{
    protected $oidcService;
    protected $provider;

    public function __construct(OIDCService $service)
    {
        $this->oidcService = $service;
        $this->provider = $service->getProvider();
    }

    public function callback(Request $request)
    {
        $error = $request->get('error');
        if (!is_null($error)) {
            throw new AuthenticationErrorException($error);
        }

        if ($request->get('state') !== $this->oidcService->getState()) {
            throw new InvalidStateException();
        }

        if (!$request->has('code')) {
            throw new AuthenticationException('No authorization code received');
        }
        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $request->get('code'),
        ]);

        $required = config('oidc-auth.required_claims');
        if (is_array($required)) {
            $claims = $token->getIdToken()->claims();
            foreach ($required as $key => $value) {
                if (!$claims->has($key)) {
                    abort(403);
                }
                $claim = $claims->get($key);

                if (is_array($value)) {
                    if (!is_array($claim)) {
                        abort(403);
                    }

                    if (array_intersect($value, $claim) !== $value) {
                        abort(403);
                    }
                } elseif ($claim !== $value) {
                    abort(403);
                }
            }
        } elseif ($required instanceof \Closure) {
            if (!$required($token->getIdToken())) {
                abort(403);
            }
        }

        $this->oidcService->storeToken($token);

        $factory = app(config('oidc-auth.authenticatable_factory'));

        Auth::login($factory->authenticatable($token));

        return redirect()->intended(config('oidc-auth.redirect_path_after_login'));
    }
}
