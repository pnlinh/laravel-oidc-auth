<?php

namespace LaravelOIDCAuth\Auth;

use Illuminate\Auth\SessionGuard as BaseGuard;
use LaravelOIDCAuth\OIDCService;

class SessionGuard extends BaseGuard
{
    public function logout()
    {
        parent::logout();
        app(OIDCService::class)->clearToken();
    }
}
