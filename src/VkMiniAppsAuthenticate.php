<?php

namespace Mistersaal\VkMiniAppsAuth;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class VkMiniAppsAuthenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        auth()->validate();
        return parent::handle($request, $next, $guards);
    }

    protected function redirectTo($request)
    {

    }
}
