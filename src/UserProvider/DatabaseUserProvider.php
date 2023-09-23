<?php

namespace tp5er\think\auth\UserProvider;

use think\contract\Arrayable;
use think\db\ConnectionInterface;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\helper\Str;
use tp5er\think\auth\Contracts\Authenticatable;
use tp5er\think\auth\GenericUser\GenericUser;
use tp5er\think\auth\Model\Field;




class DatabaseUserProvider extends UserProvider
{

    /**
     * @var ConnectionInterface
     */
    private $conn;

    /**
     * @var string
     */
    private $table;


    public function __construct(ConnectionInterface $conn, $table)
    {
        $this->conn  = $conn;
        $this->table = $table;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return Authenticatable
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function retrieveById($identifier)
    {
        $user = $this->conn->table($this->table)->find($identifier);
        return $this->getGenericUser($user);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed $identifier
     * @param string $token
     * @return GenericUser|null
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
     * @throws DbException
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $this->conn->table($this->table)
            ->where($user->getAuthIdentifierName(), $user->getAuthIdentifier())
            ->update([$user->getRememberTokenName() => $token]);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return Authenticatable
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                array_key_exists(Field::$password, $credentials))) {
            return;
        }
        $query = $this->conn->table($this->table);

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
        $user = $query->find();
        return $this->getGenericUser($user);
    }
}