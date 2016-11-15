<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Authorizer\SamlAuthorizer;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class SamlAuthorizerTest extends TestCase
{
    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidCredentialsException
     */
    public function testInvalidCredentialsThrowsException()
    {
        $saml_authorizer = new SamlAuthorizer(new Repository());

        $saml_authorizer->verifyCredentials(['username' => null]);
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\UserNotFoundException
     * @expectedExceptionMessage User not found
     */
    public function testUserNotFoundThrowsException()
    {
        $saml_authorizer = new SamlAuthorizer(new Repository());

        $saml_authorizer->verifyCredentials(['username' => 'user']);
    }

    public function testUserIsAuthenticated()
    {
        $saml_authorizer = new SamlAuthorizer(new Repository([
            'john@doe.com' => new AuthenticatedUser(1, 'john@doe.com', 'John', 'password', true),
        ]));

        $user = $saml_authorizer->verifyCredentials(['username' => 'john@doe.com']);

        $this->assertSame(1, $user->getId());
    }
}
