<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Token;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use DateTimeInterface;

interface RepositoryInterface
{
    /**
     * Find session by session ID.
     */
    public function getById(string $token_id): ?TokenInterface;

    /**
     * Return number of times that a token with the given ID was used.
     */
    public function getUsageById(string $token_id): int;

    /**
     * Return number of times that a token with the given ID was used.
     */
    public function getUsageByToken(TokenInterface $token): int;

    /**
     * Record that token with the given ID was used.
     */
    public function recordUsageById(string $token_id): void;

    /**
     * Record that token with the given ID was used.
     */
    public function recordUsageByToken(TokenInterface $token): void;

    /**
     * Issue a new token.
     */
    public function issueToken(
        AuthenticatedUserInterface $user,
        array $credentials = [],
        DateTimeInterface $expires_at = null,
    ): TokenInterface;

    /**
     * Terminate a token.
     */
    public function terminateToken(TokenInterface $token): void;
}
