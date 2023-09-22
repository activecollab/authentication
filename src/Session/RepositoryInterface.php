<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Session;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use DateTimeInterface;

interface RepositoryInterface
{
    /**
     * Find session by session ID.
     */
    public function getById(string $session_id): ?SessionInterface;

    /**
     * Return number of times that session with the given ID was used.
     */
    public function getUsageById(string $session_id): int;

    /**
     * Return number of times that session with the given ID was used.
     */
    public function getUsageBySession(SessionInterface $session): int;

    /**
     * Record that session with the given ID was used.
     */
    public function recordUsageById(string $session_id): void;

    /**
     * Record that session with the given ID was used.
     */
    public function recordUsageBySession(SessionInterface $session): void;

    /**
     * Create a new session.
     */
    public function createSession(
        AuthenticatedUserInterface $user,
        array $credentials = [],
        DateTimeInterface $expires_at = null,
    ): SessionInterface;

    /**
     * Terminate a session.
     */
    public function terminateSession(SessionInterface $session): void;
}
