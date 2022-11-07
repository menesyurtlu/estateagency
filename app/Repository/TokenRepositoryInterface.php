<?php

namespace App\Repository;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Collection;

interface TokenRepositoryInterface extends EloquentRepositoryInterface
{
    /*
     * Check if token that related to user, is exist.
     */
    public function exists(CanResetPasswordContract $user, $token);

    /*
     * Get recently created token
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user);

    /*
     * Delete token
     */
    public function delete(CanResetPasswordContract $user);

    /*
     * Delete all expired tokens
     */
    public function deleteExpired();

    /*
     * Revoke an access token.
     */
    public function revokeAccessToken($tokenId);

    /*
     * Revoke all of the token's refresh tokens.
     */
    public function revokeRefreshTokensByAccessTokenId($tokenId);

    /*
     * Get tokens by userId
     */
    public function findByUserId($userId): Collection;

    /*
     * Purge user's old tokens
     */
    public function purgeOldTokens($userId);
}
