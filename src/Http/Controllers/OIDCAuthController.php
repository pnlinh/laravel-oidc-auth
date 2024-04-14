<?php

namespace LaravelOIDCAuth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use LaravelOIDCAuth\OIDCService;

class OIDCAuthController extends Controller
{
    public function login()
    {
        return redirect()->away(app(OIDCService::class)->buildAuthorizationUrl());
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->away(config('oidc-auth.redirect_url_after_logout'));
    }
}
