<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\Token;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Token\RepositoryInterface;
use ActiveCollab\Authentication\Token\TokenInterface;
use DateTimeInterface;

class Repository implements RepositoryInterface
{
    private array $tokens;
    private array $used_tokens = [];

    public function __construct(array $tokens = [])
    {
        $this->tokens = $tokens;
    }

    public function getById(string $token_id): ?TokenInterface
    {
        return $this->tokens[$token_id] ?? null;
    }

    public function getUsageById(string $token_id): int
    {
        return empty($this->used_tokens[$token_id]) ? 0 : $this->used_tokens[$token_id];
    }

    public function getUsageByToken(TokenInterface $token): int
    {
        return $this->getUsageById($token->getTokenId());
    }

    public function recordUsageById($token_id): void
    {
        if (empty($this->used_tokens[$token_id])) {
            $this->used_tokens[$token_id] = 0;
        }

        ++$this->used_tokens[$token_id];
    }

    public function recordUsageByToken(TokenInterface $token): void
    {
        $this->recordUsageById($token->getTokenId());
    }

    public function issueToken(
        AuthenticatedUserInterface $user,
        array $credentials = [],
        DateTimeInterface $expires_at = null,
    ): TokenInterface
    {
        $token_id = $this->tokens[$user->getEmail()] ?? sha1((string) time());

        $token = new Token($token_id, $user->getUsername(), $expires_at);

        if (!empty($credentials['extra_attribute'])) {
            $token->setExtraAttribute($credentials['extra_attribute']);
        }

        return $token;
    }

    public function terminateToken(TokenInterface $token): void
    {
        foreach ($this->tokens as $k => $v) {
            if ($v->getTokenId() == $token->getTokenId()) {
                unset($this->tokens[$k]);
            }
        }
    }
}
