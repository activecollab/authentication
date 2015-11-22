<?php

namespace ActiveCollab\Authentication\Token;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Token\TokenInterface;

/**
 * @package ActiveCollab\Authentication\Token
 */
interface RepositoryInterface
{
    /**
     * Find session by session ID
     *
     * @param  string              $token_id
     * @return TokenInterface|null
     */
    public function getById($token_id);

    /**
     * Return number of times that a token with the give ID was used
     *
     * @param  TokenInterface|string $token_or_token_id
     * @return integer
     */
    public function getUsageById($token_or_token_id);

    /**
     * Record that token with the given ID was used
     *
     * @param TokenInterface|string $token_or_token_id
     */
    public function recordUsage($token_or_token_id);

    /**
     * Issue a new token
     *
     * @param  AuthenticatedUserInterface $user
     * @param  \DateTimeInterface|null    $expires_at
     * @return TokenInterface
     */
    public function issueToken(AuthenticatedUserInterface $user, \DateTimeInterface $expires_at = null);
}
