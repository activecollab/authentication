<?php

namespace ActiveCollab\Authentication\Session;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;

/**
 * @package ActiveCollab\Authentication\Token
 */
interface RepositoryInterface
{
    /**
     * Find session by session ID
     *
     * @param  string                $session_id
     * @return SessionInterface|null
     */
    public function getById($session_id);

    /**
     * Return number of times that session with the give ID was used
     *
     * @param  string  $session_id
     * @return integer
     */
    public function getUsageById($session_id);

    /**
     * Record that session with the given ID was used
     *
     * @param string $session_or_session_id
     */
    public function recordSessionUsage($session_or_session_id);

    /**
     * Issue a new token
     *
     * @param  AuthenticatedUserInterface $user
     * @param  \DateTimeInterface|null    $expires_at
     * @return SessionInterface
     */
    public function createSession(AuthenticatedUserInterface $user, \DateTimeInterface $expires_at = null);

//    public function extendSession();
//
//    public function destroySession();
}
