<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Fixtures;

use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;

/**
 * @package ActiveCollab\Authentication\Test\Authorizer
 */
class Authorizer implements AuthorizerInterface
{
    private $username = 'john@doe.com';

    /**
     * @var AuthenticatedUser
     */
    private $authenticated_user;

    /**
     * @param AuthenticatedUser $authenticated_user
     */
    public function __construct(AuthenticatedUser $authenticated_user)
    {
        $this->authenticated_user = $authenticated_user;
    }

    public function verifyCredentials(array $payload)
    {
        if (isset($payload['username']) && $payload['username'] === $this->username) {
            return $this->authenticated_user;
        }
    }

    public function onLogin(array $payload)
    {
    }

    public function onLogout(array $payload)
    {
    }
}
