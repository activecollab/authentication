<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Session;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use DateTimeInterface;

/**
 * @package ActiveCollab\Authentication\Session
 */
interface RepositoryInterface
{
    /**
     * Find session by session ID.
     *
     * @param  string                $session_id
     * @return SessionInterface|null
     */
    public function getById($session_id);

    /**
     * Return number of times that session with the given ID was used.
     *
     * @param  string $session_id
     * @return int
     */
    public function getUsageById($session_id);

    /**
     * Return number of times that session with the given ID was used.
     *
     * @param  SessionInterface $session
     * @return int
     */
    public function getUsageBySession(SessionInterface $session);

    /**
     * Record that session with the given ID was used.
     *
     * @param string $session_id
     */
    public function recordUsageById($session_id);

    /**
     * Record that session with the given ID was used.
     *
     * @param SessionInterface $session
     */
    public function recordUsageBySession(SessionInterface $session);

    /**
     * Create a new session.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  array                      $credentials
     * @param  DateTimeInterface|null     $expires_at
     * @return SessionInterface
     */
    public function createSession(AuthenticatedUserInterface $user, array $credentials = [], DateTimeInterface $expires_at = null);

    /**
     * Terminate a session.
     *
     * @param SessionInterface $session
     */
    public function terminateSession(SessionInterface $session);
}
