<?php

namespace Anafro\Biosphere\Providers;

use Anafro\Biosphere\Channels\ChannelRegistrar;
use Anafro\Biosphere\Commands\BiosphereServe;
use Anafro\Biosphere\Http\Middlewares\EnsureFromBiosphereProxy;
use Anafro\Biosphere\Tokens\TokenManager;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\ServiceProvider;

class BiosphereServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        resolve('router')->aliasMiddleware('biosphere.proxy', EnsureFromBiosphereProxy::class);
    }

    public function register(): void
    {
        VerifyCsrfToken::except('biosphere/authorize');

        if ($this->app->runningInConsole()) {
            $this->commands([
                BiosphereServe::class,
            ]);
        }

        $this->app->singleton(ChannelRegistrar::class, function (): ChannelRegistrar {
            return new ChannelRegistrar();
        });

        $this->app->singleton(TokenManager::class, function (): TokenManager {
            return new TokenManager();
        });
    }
}
