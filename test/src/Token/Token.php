<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\Token;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\Token\TokenInterface;
use DateTimeInterface;
use JsonSerializable;

class Token implements TokenInterface
{
    private mixed $extra_attribute;

    public function __construct(
        private string $token,
        private string $user_id,
        private ?DateTimeInterface $expires_at = null,
    )
    {
    }

    public function getTokenId(): string
    {
        return $this->token;
    }

    public function getAuthenticatedUser(UserRepositoryInterface $repository): AuthenticatedUserInterface
    {
        return $repository->findByUsername($this->user_id);
    }

    public function getExtraAttribute(): mixed
    {
        return $this->extra_attribute;
    }

    public function setExtraAttribute(mixed $value): static
    {
        $this->extra_attribute = $value;

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

        return ['token' => $this->token, 'user_id' => $this->user_id, 'expires_at' => $expires_at];
    }
}
