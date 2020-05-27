<?php

namespace Mistersaal\VkMiniAppsAuth;

use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class VkMiniAppsAuthenticate extends Middleware
{
    public function __construct(Auth $auth)
    {
        parent::__construct($auth);
        auth()->validate();
    }

    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
