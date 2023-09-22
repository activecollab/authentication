<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\Session;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Session\RepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use DateTimeInterface;
use InvalidArgumentException;

class Repository implements RepositoryInterface
{
    private array $sessions = [];
    private array $used_session = [];

    public function __construct(array $sessions = [])
    {
        foreach ($sessions as $session) {
            if (!$session instanceof Session) {
                throw new InvalidArgumentException('Invalid session instance');
            }

            $this->sessions[$session->getSessionId()] = $session;
        }
    }

    public function getById(string $session_id): ?SessionInterface
    {
        return $this->sessions[$session_id] ?? null;
    }

    public function getUsageById(string $session_id): int
    {
        return empty($this->used_session[$session_id]) ? 0 : $this->used_session[$session_id];
    }

    public function getUsageBySession(SessionInterface $session): int
    {
        return $this->getUsageById($session->getSessionId());
    }

    public function recordUsageById(string $session_id): void
    {
        if (empty($this->used_session[$session_id])) {
            $this->used_session[$session_id] = 0;
        }

        ++$this->used_session[$session_id];
    }

    public function recordUsageBySession(SessionInterface $session): void
    {
        $this->recordUsageById($session->getSessionId());
    }

    public function createSession(
        AuthenticatedUserInterface $user,
        array $credentials = [],
        DateTimeInterface $expires_at = null,
    ): SessionInterface
    {
        /** @var Session $session */
        foreach ($this->sessions as $session) {
            if ($session->getUserId() === $user->getEmail()) {
                return $session;
            }
        }

        $session = new Session(sha1((string) time()), $user->getUsername(), $expires_at);

        if (!empty($credentials['remember'])) {
            $session->setIsExtendedSession(true);
        }

        return $session;
    }

    public function terminateSession(SessionInterface $session): void
    {
        foreach ($this->sessions as $k => $v) {
            if ($session->getSessionId() === $v->getSessionId()) {
                unset($this->sessions[$k]);
            }
        }
    }
}
