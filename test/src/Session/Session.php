<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\Session;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use DateTimeInterface;
use JsonSerializable;

class Session implements SessionInterface
{
    const SESSION_TTL = 1800;
    const EXTENDED_SESSION_TTL = 1209600;

    public bool $is_extended_session = false;

    public function __construct(
        private string $session_id,
        private string $user_id,
        private ?DateTimeInterface $expires_at = null,
    )
    {
    }

    public function getSessionId(): string
    {
        return $this->session_id;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function getAuthenticatedUser(UserRepositoryInterface $repository): AuthenticatedUserInterface
    {
        return $repository->findByUsername($this->user_id);
    }

    public function getSessionTtl(): int
    {
        return $this->getIsExtendedSession() ? self::EXTENDED_SESSION_TTL : self::SESSION_TTL;
    }

    public function getIsExtendedSession(): bool
    {
        return $this->is_extended_session;
    }

    public function setIsExtendedSession(bool $value): static
    {
        $this->is_extended_session = $value;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $expires_at = null;

        if ($this->expires_at instanceof JsonSerializable) {
            $expires_at = $this->expires_at->jsonSerialize();
        } elseif ($this->expires_at instanceof DateTimeInterface) {
            $expires_at = $this->expires_at->getTimestamp();
        }

        return [
            'session_id' => $this->session_id,
            'user_id' => $this->user_id,
            'expires_at' => $expires_at,
        ];
    }

    public function extendSession(int $reference_timestamp = null): void
    {
    }
}
