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

use Closure;
use think\contract\Arrayable;
use think\db\ConnectionInterface;
use think\db\PDOConnection;
use think\helper\Str;
use tp5er\think\auth\contracts\Authenticatable;
use tp5er\think\auth\contracts\UserProvider;
use tp5er\think\auth\exceptions\CredentialsException;
use tp5er\think\hashing\Hasher;

class DatabaseUserProvider implements UserProvider
{
    /**
     * @var PDOConnection
     */
    protected $conn;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * @param ConnectionInterface $conn
     * @param Hasher $hasher
     * @param string $table
     */
    public function __construct(
        ConnectionInterface $conn,
        Hasher              $hasher,
        $table
    ) {
        $this->conn = $conn;
        $this->table = $table;
        $this->hasher = $hasher;
    }

    /**
     * @param $identifier
     *
     * @return Authenticatable|GenericUser|null
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function retrieveById($identifier)
    {
        $user = $this->conn->table($this->table)->find($identifier);

        return $this->getGenericUser($user);
    }

    /**
     * @param $identifier
     * @param $token
     *
     * @return Authenticatable|void|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $user = $this->getGenericUser(
            $this->conn->table($this->table)->find($identifier)
        );

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
        $this->conn->table($this->table)
            ->where($user->getAuthIdentifierName(), $user->getAuthIdentifier())
            ->update([$user->getRememberTokenName() => $token]);
    }

    /**
     * @param array $credentials
     *
     * @return Authenticatable|GenericUser|void|null
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                array_key_exists("password", $credentials))) {
            throw new CredentialsException(
                CredentialsException::codeMissing,
                $this->table,
                $credentials
            );
        }
        $query = $this->conn->table($this->table);

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, "password")) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } elseif ($value instanceof Closure) {
                $value($query);
            } else {
                $query->where($key, $value);
            }
        }
        // Now we are ready to execute the query to see if we have an user matching
        // the given credentials. If not, we will just return nulls and indicate
        // that there are no matching users for these given credential arrays.
        $user = $query->find();
        if (is_null($user)) {
            throw new CredentialsException(
                CredentialsException::codeNoRecordFound,
                $this->table,
                $credentials
            );
        }

        return $this->getGenericUser($user);
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
     * Get the generic user.
     *
     * @param mixed $user
     *
     * @return GenericUser|null
     */
    protected function getGenericUser($user)
    {
        return new GenericUser((array) $user);
    }
}
