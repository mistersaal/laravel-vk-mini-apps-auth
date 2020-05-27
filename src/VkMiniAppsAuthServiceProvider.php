<?php

namespace Mistersaal\VkMiniAppsAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class VkMiniAppsAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->publishes([
            __DIR__ . '/../config/vkminiapps.php' => config_path('vkminiapps.php'),
        ]);
    }

    public function boot()
    {
        $this->app['router']->aliasMiddleware('auth.vk' , VkMiniAppsAuthenticate::class);

        Auth::provider('vkMiniApps', function ($app, array $config) {
            return new VkUserProvider($config['model']);
        });

        Auth::extend('vkSign', function ($app, $name, array $config) {
            return new VkGuard(
                Auth::createUserProvider($config['provider']),
                $app->make('request'),
                $app->make(VkSign::class)
            );
        });
    }
}
