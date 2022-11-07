<?php

namespace App\Repository;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface extends EloquentRepositoryInterface
{

    public function retrieveById($identifier);

    public function retrieveByToken($identifier, $token);

    public function updateRememberToken($userId, $token);

    public function retrieveByCredentials(array $credentials);

    public function validateCredentials($userId,array $credentials);

    /*
     * Gets user by email
     */
    public function findByEmail(string $email): ?Model;
}
