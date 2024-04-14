<?php

namespace LaravelOIDCAuth\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use LaravelOIDCAuth\OIDCService;

class Authenticate extends Middleware
{
    protected $oidcService;

    public function __construct(Auth $auth, OIDCService $service)
    {
        $this->oidcService = $service;
        parent::__construct($auth);
    }

    protected function redirectTo(Request $request)
    {
        if (! $request->expectsJson()) {
            return $this->oidcService->buildAuthorizationUrl();
        }
    }
}
