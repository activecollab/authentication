<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Session;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Session\RepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use DateTimeInterface;
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
     * @var array
     */
    private $used_session = [];

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
     * Find session by session ID.
     *
     * @param  string                $session_id
     * @return SessionInterface|null
     */
    public function getById($session_id)
    {
        return isset($this->sessions[$session_id]) ? $this->sessions[$session_id] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsageById($session_id)
    {
        return empty($this->used_session[$session_id]) ? 0 : $this->used_session[$session_id];
    }

    /**
     * {@inheritdoc}
     */
    public function getUsageBySession(SessionInterface $session)
    {
        return $this->getUsageById($session->getSessionId());
    }

    /**
     * {@inheritdoc}
     */
    public function recordUsageById($session_id)
    {
        if (empty($this->used_session[$session_id])) {
            $this->used_session[$session_id] = 0;
        }

        ++$this->used_session[$session_id];
    }

    /**
     * {@inheritdoc}
     */
    public function recordUsageBySession(SessionInterface $session)
    {
        $this->recordUsageById($session->getSessionId());
    }

    /**
     * Create a new session.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  DateTimeInterface|null     $expires_at
     * @return SessionInterface
     */
    public function createSession(AuthenticatedUserInterface $user, DateTimeInterface $expires_at = null)
    {
        /** @var Session $session */
        foreach ($this->sessions as $session) {
            if ($session->getUserId() === $user->getEmail()) {
                return $session;
            }
        }

        return new Session(sha1(time()), $user->getUsername(), $expires_at);
    }

    /**
     * Terminate a session.
     *
     * @param SessionInterface $session
     */
    public function terminateSession(SessionInterface $session)
    {
        foreach ($this->sessions as $k => $v) {
            if ($session->getSessionId() === $v->getSessionId()) {
                unset($this->sessions[$k]);
            }
        }
    }
}
