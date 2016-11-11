<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Token;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Token\RepositoryInterface;
use ActiveCollab\Authentication\Token\TokenInterface;
use DateTimeInterface;

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
     * @var array
     */
    private $used_tokens = [];

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
     * {@inheritdoc}
     */
    public function getUsageById($token_id)
    {
        return empty($this->used_tokens[$token_id]) ? 0 : $this->used_tokens[$token_id];
    }

    /**
     * {@inheritdoc}
     */
    public function getUsageByToken(TokenInterface $token)
    {
        return $this->getUsageById($token->getTokenId());
    }

    /**
     * {@inheritdoc}
     */
    public function recordUsageById($token_id)
    {
        if (empty($this->used_tokens[$token_id])) {
            $this->used_tokens[$token_id] = 0;
        }

        ++$this->used_tokens[$token_id];
    }

    /**
     * {@inheritdoc}
     */
    public function recordUsageByToken(TokenInterface $token)
    {
        $this->recordUsageById($token->getTokenId());
    }

    /**
     * {@inheritdoc}
     */
    public function issueToken(AuthenticatedUserInterface $user, array $credentials = [], DateTimeInterface $expires_at = null)
    {
        $token_id = isset($this->tokens[$user->getEmail()]) ? $this->tokens[$user->getEmail()] : sha1(time());

        $token = new Token($token_id, $user->getUsername(), $expires_at);

        if (!empty($credentials['extra_attribute'])) {
            $token->setExtraAttribute($credentials['extra_attribute']);
        }

        return $token;
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
