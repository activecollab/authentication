<?php

namespace ActiveCollab\Authentication\Test\Session;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Session\RepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;

/**
 * @package ActiveCollab\Authentication\Test\Session
 */
class Repository implements RepositoryInterface
{
    /**
     * @var array
     */
    private $prepared_session_ids;

    /**
     * @param array $prepared_sessions_ids
     */
    public function __construct(array $prepared_sessions_ids = [])
    {
        $this->prepared_session_ids = $prepared_sessions_ids;
    }

    /**
     * Create a new session
     *
     * @param  AuthenticatedUserInterface $user
     * @param  \DateTimeInterface|null    $expires_at
     * @return SessionInterface
     */
    public function createSession(AuthenticatedUserInterface $user, \DateTimeInterface $expires_at = null)
    {
        $session_id = isset($this->prepared_session_ids[$user->getEmail()]) ? $this->prepared_session_ids[$user->getEmail()] : sha1(time());

        return new Session($session_id, $expires_at);
    }
}
