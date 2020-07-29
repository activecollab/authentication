<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Authorizer\SamlAuthorizer;
use ActiveCollab\Authentication\Exception\InvalidCredentialsException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

class SamlAuthorizerTest extends TestCase
{
    public function testInvalidCredentialsThrowsException()
    {
        $this->expectException(InvalidCredentialsException::class);

        $saml_authorizer = new SamlAuthorizer(new Repository());

        $saml_authorizer->verifyCredentials(['username' => null]);
    }

    public function testUserNotFoundThrowsException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");

        $saml_authorizer = new SamlAuthorizer(new Repository());

        $saml_authorizer->verifyCredentials(['username' => 'user']);
    }

    public function testUserIsAuthenticated()
    {
        $saml_authorizer = new SamlAuthorizer(
            new Repository(
                [
                    'john@doe.com' => new AuthenticatedUser(
                        1,
                        'john@doe.com',
                        'John',
                        'password',
                        true
                    ),
                ]
            )
        );

        $user = $saml_authorizer->verifyCredentials(
            [
                'username' => 'john@doe.com'
            ]
        );

        $this->assertSame(1, $user->getId());
    }
}
