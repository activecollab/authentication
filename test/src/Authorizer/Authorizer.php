<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Authorizer;

use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;

/**
 * @package ActiveCollab\Authentication\Test\Authorizer
 */
class Authorizer implements AuthorizerInterface
{
    private $username = 'john@doe.com';

    public function verifyCredentials(array $payload)
    {
        return isset($payload['username']) && $payload['username'] === $this->username
            ? true
            : false;
    }

    public function onLogin(array $payload)
    {
    }

    public function onLogout(array $payload)
    {
    }
}
