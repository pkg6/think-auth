<?php

/*
 * This file is part of the tp5er/think-auth
 *
 * (c) pkg6 <https://github.com/pkg6>
 *
 * (L) Licensed <https://opensource.org/license/MIT>
 *
 * (A) zhiqiang <https://www.zhiqiang.wang>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace tp5er\think\auth;

use think\contract\Arrayable;
use think\helper\Str;
use think\Model;
use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\contracts\UserProvider;
use tp5er\think\auth\exceptions\CredentialsException;
use tp5er\think\hashing\Hasher;

class EloquentUserProvider implements UserProvider
{
    /**
     * @var Hasher
     */
    protected $hasher;
    /**
     * @var string
     */
    protected $model;

    /**
     * @param Hasher $hasher
     * @param string $model
     */
    public function __construct(Hasher $hasher, $model)
    {
        $this->model = $model;
        $this->hasher = $hasher;
    }

    /**
     * @param $identifier
     *
     * @return array|mixed|Model|Authenticatable|null
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->find();
    }

    /**
     * @param $identifier
     * @param $token
     *
     * @return array|mixed|Model|Authenticatable|null
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function retrieveByToken($identifier, $token)
    {
        $user = $this->retrieveById($identifier);

        return $user && $user->getRememberToken() && hash_equals($user->getRememberToken(), $token)
            ? $user : null;
    }

    /**
     * @param Authenticatable $user
     * @param $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    /**
     * @param array $credentials
     *
     * @return Authenticatable|GenericUser|null
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                Str::contains($this->firstCredentialKey($credentials), "password"))) {
            throw new CredentialsException(
                CredentialsException::codeMissing,
                $this->model,
                $credentials
            );
        }
        $query = $this->newModelQuery();
        foreach ($credentials as $key => $value) {
            if (Str::contains($key, "password")) {
                continue;
            }
            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        $user = $query->find();
        if (is_null($user)) {
            throw new CredentialsException(
                CredentialsException::codeNoRecordFound,
                $this->model,
                $credentials
            );
        }

        return $user;
    }

    /**
     * @param Authenticatable $user
     * @param array $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->hasher->check(
            $credentials['password'],
            $user->getAuthPassword()
        );
    }

    /**
     * @param null $model
     *
     * @return Model
     */
    protected function newModelQuery($model = null)
    {
        return is_null($model) ? $this->createModel()->newQuery() : $model->newQuery();
    }

    /**
     * Create a new instance of the model.
     *
     * @return Model
     */
    protected function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Get the first key from the credential array.
     *
     * @param array $credentials
     *
     * @return string|null
     */
    protected function firstCredentialKey(array $credentials)
    {
        foreach ($credentials as $key => $value) {
            return $key;
        }

        return null;
    }
}
