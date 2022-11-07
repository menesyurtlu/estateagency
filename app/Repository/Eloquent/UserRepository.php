<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\UserRepositoryInterface;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /*
     *  Gets user by email
     */
    public function findByEmail(string $email): ?Model
    {
        // TODO: Implement findByEmail() method.
        return $this->model->where("email", $email)->first();
    }

    /**
     * @param $identifier
     * @return mixed
     */
    public function retrieveById($identifier)
    {
        // TODO: Implement retrieveById() method.
        return $this->model
            ->where($this->model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    /**
     * @param $identifier
     * @param $token
     * @return mixed
     */
    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.

        $retrievedModel = $this->model->where(
            $this->model->getAuthIdentifierName(), $identifier
        )->first();

        if (! $retrievedModel) {
            return;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token)
            ? $retrievedModel : null;
    }

    /**
     * @param $userId
     * @param $token
     * @return void
     */
    public function updateRememberToken($userId, $token)
    {
        // TODO: Implement updateRememberToken() method.
        $user = $this->model->find($userId);

        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    /**
     * @param array $credentials
     * @return mixed
     */
    public function retrieveByCredentials(array $credentials)
    {
        // TODO: Implement retrieveByCredentials() method.
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                Str::contains($this->firstCredentialKey($credentials), 'password'))) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->model;

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
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

        return $query->first();
    }

    /**
     * @param array $credentials
     * @return mixed
     */
    public function validateCredentials($userId, array $credentials)
    {
        // TODO: Implement validateCredentials() method.
        $user = $this->model->find($userId);
        return Hash::check($credentials["password"], $user->getAuthPassword());
    }

    /**
     * Get the first key from the credential array.
     *
     * @param  array  $credentials
     * @return string|null
     */
    protected function firstCredentialKey(array $credentials)
    {
        foreach ($credentials as $key => $value) {
            return $key;
        }
    }
}
