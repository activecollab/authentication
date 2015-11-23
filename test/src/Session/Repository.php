<?php

namespace ActiveCollab\Authentication\Test\Session;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Session\RepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\Authentication\Test\Session
 */
class Repository implements RepositoryInterface
{
    /**
     * @var SessionInterface[]
     */
    private $sessions = [];

    /**
     * @param Session[] $sessions
     */
    public function __construct(array $sessions = [])
    {
        foreach ($sessions as $session) {
            if ($session instanceof Session) {
                $this->sessions[$session->getSessionId()] = $session;
            } else {
                throw new InvalidArgumentException('Invalid session instance');
            }
        }
    }

    /**
     * Find session by session ID
     *
     * @param  string                $session_id
     * @return SessionInterface|null
     */
    public function getById($session_id)
    {
        return isset($this->sessions[$session_id]) ? $this->sessions[$session_id] : null;
    }

    /**
     * @var array
     */
    private $used_session = [];

    /**
     * {@inheritdoc}
     */
    public function getUsageById($session_or_session_id)
    {
        $session_id = $session_or_session_id instanceof SessionInterface ? $session_or_session_id->getSessionId() : $session_or_session_id;

        return empty($this->used_session[$session_id]) ? 0 : $this->used_session[$session_id];
    }

    /**
     * {@inheritdoc}
     */
    public function recordUsage($session_or_session_id)
    {
        $session_id = $session_or_session_id instanceof SessionInterface ? $session_or_session_id->getSessionId() : $session_or_session_id;

        if (empty($this->used_session[$session_id])) {
            $this->used_session[$session_id] = 0;
        }

        $this->used_session[$session_id]++;
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
        /** @var Session $session */
        foreach ($this->sessions as $session) {
            if ($session->getUserId() == $user->getEmail()) {
                return $session;
            }
        }

        return new Session(sha1(time()), $user->getUsername(), $expires_at);
    }

    /**
     * Terminate a session
     *
     * @param SessionInterface $session
     */
    public function terminateSession(SessionInterface $session)
    {
        foreach ($this->sessions as $k => $v) {
            if ($session->getSessionId() == $v->getSessionId()) {
                unset($this->sessions[$k]);
            }
        }
    }
}
