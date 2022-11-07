<?php

namespace App\Authentication;

use App\Models\User;
use App\Repository\Eloquent\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as IlluminateUserProvider;
use Illuminate\Auth\EloquentUserProvider as EloquentUserProvider;

class UserProvider implements IlluminateUserProvider
{
    /**
     * @param mixed $identifier
     * @return mixed
     */
    public function retrieveById($identifier)
    {
        // Get and return a user by their unique identifier
        return (new UserRepository(new User()))->findById($identifier);
    }

    /**
     * @param mixed $identifier
     * @param string $token
     * @return Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // Get and return a user by their unique identifier and "remember me" token
        return (new UserRepository(new User()))->retrieveByToken($identifier, $token);
    }

    /**
     * @param integer $userId
     * @param string $token
     * @return void
     */
    public function updateRememberToken($userId, $token)
    {
        // Save the given "remember me" token for the given user
        (new UserRepository(new User()))->updateRememberToken($userId, $token);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // Get and return a user by looking up the given credentials
        if (empty($credentials)) {
            return;
        }

        return (new UserRepository(new User()))->retrieveByCredentials($credentials);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Check that given credentials belong to the given user
        return (new UserRepository(new User()))->validateCredentials($user->id, $credentials);
    }

}
