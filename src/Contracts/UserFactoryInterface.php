<?php

namespace LaravelOIDCAuth\Contracts;

use OpenIDConnectClient\AccessToken;

interface UserFactoryInterface
{
    /**
     * Get Illuminate\Contracts\Auth\Authenticatable from access token.
     */
    public function authenticatable(AccessToken $token);
}
