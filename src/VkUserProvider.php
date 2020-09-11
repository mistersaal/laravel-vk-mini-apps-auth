<?php


namespace Mistersaal\VkMiniAppsAuth;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Mistersaal\VkMiniAppsAuth\Exceptions\VkAuthModelException;

class VkUserProvider implements UserProvider
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     * @throws VkAuthModelException
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');
        $model = new $class;
        if (! $model instanceof VkMiniAppsAuthenticatable) {
            throw new VkAuthModelException("Auth model doesn't implement VkMiniAppsAuthenticatable");
        }
        return new $model;
    }

    /**
     * @param $identifier
     * @return mixed
     * @throws VkAuthModelException
     */
    public function retrieveById($identifier)
    {
        return $this->createModel()->find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        //
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        //
    }

    /**
     * @param array $credentials
     * @return mixed
     * @throws VkAuthModelException
     */
    public function retrieveByCredentials(array $credentials)
    {
        /** @var VkMiniAppsAuthenticatable|Authenticatable $model */
        $model = $this->createModel();
        return $model->firstWhere($model->getVkIdFieldName(), $credentials['vk_user_id']);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        /** @var VkMiniAppsAuthenticatable $user */
        return $user->{$user->getVkIdFieldName()} == $credentials['vk_user_id'];
    }
}
