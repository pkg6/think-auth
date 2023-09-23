<?php


namespace tp5er\think\auth\UserProvider;

use think\contract\Arrayable;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\helper\Str;
use think\Model;
use tp5er\think\auth\Contracts\Authenticatable;
use tp5er\think\auth\Model\Field;



class ModelUserProvider extends UserProvider
{

    /**
     * @var \think\model;
     */
    protected $model;

    /**
     * ModelUserProvider constructor.
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return Authenticatable|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();
        return $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->find();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed $identifier
     * @param string $token
     * @return Authenticatable|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function retrieveByToken($identifier, $token)
    {
        $user = $this->retrieveById($identifier);
        return $user && $user->getRememberToken() && hash_equals($user->getRememberToken(), $token)
            ? $user : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param Authenticatable $user
     * @param string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return Authenticatable|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                Str::contains($this->firstCredentialKey($credentials), Field::$password))) {
            return;
        }
        $query = $this->newModelQuery();
        foreach ($credentials as $key => $value) {
            if (Str::contains($key, Field::$password)) {
                continue;
            }
            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        if (is_null($user = $query->find())) {
            return;
        }
        return $this->getGenericUser($user->toArray());
    }


    /**
     * @param null $model
     * @return BaseQuery
     */
    protected function newModelQuery($model = null)
    {
        return is_null($model)
            ? $this->createModel()->newQuery()
            : $model->newQuery();
    }

    /**
     * Get the first key from the credential array.
     *
     * @param array $credentials
     * @return string|null
     */
    protected function firstCredentialKey(array $credentials)
    {
        foreach ($credentials as $key => $value) {
            return $key;
        }
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
}