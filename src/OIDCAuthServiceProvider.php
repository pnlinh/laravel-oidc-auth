<?php

namespace LaravelOIDCAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use LaravelOIDCAuth\Auth\SessionGuard;
use LaravelOIDCAuth\Auth\SessionUserProvider;

class OIDCAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/oidc-auth.php' => config_path('oidc-auth.php'),
        ]);
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Auth::provider('oidc-auth-session', function () {
            return app(SessionUserProvider::class);
        });
        Auth::extend('oidc-auth-session', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider'] ?? null);
            $guard = new SessionGuard($name, $provider, $app['session.store']);
            $guard->setCookieJar($this->app['cookie']);

            return $guard;
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/oidc-auth.php', 'oidc-auth');
    }
}
