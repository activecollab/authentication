<?php

/*
 * This file is part of the Active Collab ID project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Token;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;

/**
 * @package ActiveCollab\Authentication\Token
 */
interface RepositoryInterface
{
    /**
     * Find session by session ID.
     *
     * @param  string              $token_id
     * @return TokenInterface|null
     */
    public function getById($token_id);

    /**
     * Return number of times that a token with the give ID was used.
     *
     * @param  TokenInterface|string $token_or_token_id
     * @return int
     */
    public function getUsageById($token_or_token_id);

    /**
     * Record that token with the given ID was used.
     *
     * @param TokenInterface|string $token_or_token_id
     */
    public function recordUsage($token_or_token_id);

    /**
     * Issue a new token.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  \DateTimeInterface|null    $expires_at
     * @return TokenInterface
     */
    public function issueToken(AuthenticatedUserInterface $user, \DateTimeInterface $expires_at = null);

    /**
     * Terminate a token.
     *
     * @param TokenInterface $token
     */
    public function terminateToken(TokenInterface $token);
}
