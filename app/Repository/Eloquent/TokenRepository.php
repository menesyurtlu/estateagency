<?php

namespace App\Repository\Eloquent;

use App\Repository\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Token;

class TokenRepository extends BaseRepository implements TokenRepositoryInterface
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
    public function __construct(Token $model)
    {
        $this->model = $model;
    }

    /**
     * @param CanResetPasswordContract $user
     * @param $token
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        // TODO: Implement exists() method.
    }

    /**
     * @param CanResetPasswordContract $user
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        // TODO: Implement recentlyCreatedToken() method.
    }

    /**
     * @param CanResetPasswordContract $user
     */
    public function delete(CanResetPasswordContract $user)
    {
        // TODO: Implement delete() method.
    }

    /**
     *
     */
    public function deleteExpired()
    {
        // TODO: Implement deleteExpired() method.
    }

    /*
     * Find all records by UserID
     */
    /**
     * @param $userId
     * @return Collection
     */
    public function findByUserId($userId): Collection
    {
        // TODO: Implement findByUserId() method.
        return $this->model->where('user_id', $userId);
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function purgeOldTokens($userId)
    {
        // TODO: Implement purgeOldTokens() method.
        return $this->model->where('user_id', $userId)->delete();
    }

    /**
     * @param $tokenId
     * @return mixed
     */
    public function revokeAccessToken($tokenId)
    {
        // TODO: Implement revokeAccessToken() method.
        return $this->model->find($tokenId)->update([
            "revoked" => t
        ]);
    }

    /**
     * @param $tokenId
     * @return mixed
     */
    public function revokeRefreshTokensByAccessTokenId($tokenId)
    {
        // TODO: Implement revokeRefreshTokensByAccessTokenId() method.
    }
}
