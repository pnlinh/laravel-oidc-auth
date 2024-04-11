<?php

namespace LaravelOIDCAuth;

use LaravelOIDCAuth\Contracts\UserFactoryInterface;
use OpenIDConnectClient\AccessToken;

class UserFactory implements UserFactoryInterface
{
    public function authenticatable(AccessToken $token)
    {
        return new OIDCUser($token);
    }
}
