<?php

/*
 * This file is part of the Active Collab ID project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Token;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Token\RepositoryInterface;
use ActiveCollab\Authentication\Token\TokenInterface;

/**
 * @package ActiveCollab\Authentication\Test\Token
 */
class Repository implements RepositoryInterface
{
    /**
     * @var Token[]
     */
    private $tokens;

    /**
     * @param array $tokens
     */
    public function __construct(array $tokens = [])
    {
        $this->tokens = $tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($token_id)
    {
        return isset($this->tokens[$token_id]) ? $this->tokens[$token_id] : null;
    }

    /**
     * @var array
     */
    private $used_tokens = [];

    /**
     * {@inheritdoc}
     */
    public function getUsageById($token_or_token_id)
    {
        $token_id = $token_or_token_id instanceof TokenInterface ? $token_or_token_id->getTokenId() : $token_or_token_id;

        return empty($this->used_tokens[$token_id]) ? 0 : $this->used_tokens[$token_id];
    }

    /**
     * {@inheritdoc}
     */
    public function recordUsage($token_or_token_id)
    {
        $token_id = $token_or_token_id instanceof TokenInterface ? $token_or_token_id->getTokenid() : $token_or_token_id;

        if (empty($this->used_tokens[$token_id])) {
            $this->used_tokens[$token_id] = 0;
        }

        ++$this->used_tokens[$token_id];
    }

    /**
     * {@inheritdoc}
     */
    public function issueToken(AuthenticatedUserInterface $user, \DateTimeInterface $expires_at = null)
    {
        $token = isset($this->tokens[$user->getEmail()]) ? $this->tokens[$user->getEmail()] : sha1(time());

        return new Token($token, $user->getUsername(), $expires_at);
    }

    /**
     * {@inheritdoc}
     */
    public function terminateToken(TokenInterface $token)
    {
        foreach ($this->tokens as $k => $v) {
            if ($v->getTokenId() == $token->getTokenId()) {
                unset($this->tokens[$k]);
            }
        }
    }
}
