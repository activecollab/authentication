<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Token;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use DateTimeInterface;

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
     * Return number of times that a token with the given ID was used.
     *
     * @param  string $token_id
     * @return int
     */
    public function getUsageById($token_id);

    /**
     * Return number of times that a token with the given ID was used.
     *
     * @param  TokenInterface $token
     * @return int
     */
    public function getUsageByToken(TokenInterface $token);

    /**
     * Record that token with the given ID was used.
     *
     * @param string $token_id
     */
    public function recordUsageById($token_id);

    /**
     * Record that token with the given ID was used.
     *
     * @param TokenInterface $token
     */
    public function recordUsageByToken(TokenInterface $token);

    /**
     * Issue a new token.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  array                      $credentials
     * @param  DateTimeInterface|null     $expires_at
     * @return TokenInterface
     */
    public function issueToken(AuthenticatedUserInterface $user, array $credentials = [], DateTimeInterface $expires_at = null);

    /**
     * Terminate a token.
     *
     * @param TokenInterface $token
     */
    public function terminateToken(TokenInterface $token);
}
