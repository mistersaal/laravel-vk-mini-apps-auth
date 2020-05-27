<?php


namespace Mistersaal\VkMiniAppsAuth;


use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;

class VkGuard implements Guard
{
    protected $request;
    protected $provider;
    protected $user;
    protected $vkSign;

    /**
     * Create a new authentication guard.
     *
     * @param UserProvider $provider
     * @param Request $request
     * @param VkSign $vkSign
     */
    public function __construct(UserProvider $provider, Request $request, VkSign $vkSign)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->user = null;
        $this->vkSign = $vkSign;
    }

    public function check()
    {
        return ! is_null($this->user());
    }

    public function guest()
    {
        return ! $this->check();
    }

    public function user()
    {
        if (! is_null($this->user)) {
            return $this->user;
        }
    }

    public function id()
    {
        if ($user = $this->user()) {
            return $user->getAuthIdentifier();
        }
    }

    /**
     * @return array|bool
     * @throws VkSignException
     */
    public function getCredentials()
    {
        $url = $this->request->header(config('vkminiapps.signUrl.header'));
        if (! $url) {
            throw new VkSignException("No credentials.");
        }

        return $this->vkSign->getParams($url);
    }

    /**
     * @param array $credentials
     * @return bool
     * @throws VkSignException
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials)) {
            if (!$credentials = $this->getCredentials()) {
                throw new VkSignException("No credentials.");
            }
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        if (! is_null($user) && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);

            return true;
        } else {
            return false;
        }
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        return $this;
    }
}
