<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Token;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\AuthenticationResult\Implementation as AuthenticationResultInterfaceImplementation;
use ActiveCollab\Authentication\Token\TokenInterface;
use DateTimeInterface;
use JsonSerializable;

/**
 * @package ActiveCollab\Authentication\Test\Token
 */
class Token implements TokenInterface
{
    use AuthenticationResultInterfaceImplementation;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $user_id;

    /**
     * @var DateTimeInterface
     */
    private $expires_at;

    /**
     * @param string                 $token
     * @param string                 $user_id
     * @param DateTimeInterface|null $expires_at
     */
    public function __construct($token, $user_id, DateTimeInterface $expires_at = null)
    {
        $this->token = $token;
        $this->user_id = $user_id;
        $this->expires_at = $expires_at;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenId()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticatedUser(UserRepositoryInterface $repository)
    {
        return $repository->findByUsername($this->user_id);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
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
