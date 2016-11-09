<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Exception\InvalidCredentialsException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;

/**
 * @package ActiveCollab\Authentication\Authorizer
 */
class SamlAuthorizer implements AuthorizerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $user_repository;

    /**
     * @param RepositoryInterface $user_repository
     */
    public function __construct(RepositoryInterface $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    /**
     * {@inheritdoc}
     */
    public function verifyCredentials(array $payload)
    {
        if (!isset($payload['username'])) {
            throw new InvalidCredentialsException();
        }

        $user = $this->user_repository->findByUsername($payload['username']);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogin(array $payload)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onLogout(array $payload)
    {
    }
}
