<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Authorizer\LocalAuthorizer;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class LocalAuthorizerTest extends TestCase
{
    /**
     * @dataProvider providerInvalidCredentials
     * @expectedException ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException
     * @expectedExceptionMessage Authentication request data not valid
     */
    public function testInvalidCredentialsThrowsException($credentials)
    {
        $local_authorizer = new LocalAuthorizer(new Repository());

        $local_authorizer->verifyCredentials($credentials);
    }

    public function providerInvalidCredentials()
    {
        return [
            [['username' => null, 'password' => null]],
            [['username' => '', 'password' => '']],
            [['username' => 'john@doe.com', 'password' => '']],
            [['username' => '', 'password' => 'password']],
        ];
    }

    /**
     * @expectedException ActiveCollab\Authentication\Exception\UserNotFoundException
     * @expectedExceptionMessage User not found
     */
    public function testUserNotFoundThrowsException()
    {
        $local_authorizer = new LocalAuthorizer(new Repository());

        $local_authorizer->verifyCredentials(['username' => 'user', 'password' => 'password']);
    }

    /**
     * @expectedException ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password not valid
     */
    public function testInvalidPasswordThrowsException()
    {
        $local_authorizer = new LocalAuthorizer(new Repository([
            'john@doe.com' => new AuthenticatedUser(1, 'john@doe.com', 'John', 'invalid_password', true),
        ]));

        $local_authorizer->verifyCredentials(['username' => 'john@doe.com', 'password' => 'password']);
    }

    /**
     * @expectedException ActiveCollab\Authentication\Exception\UserNotFoundException
     * @expectedExceptionMessage User not found
     */
    public function testUserCanNotAuthenticateThrowsException()
    {
        $local_authorizer = new LocalAuthorizer(new Repository([
            'john@doe.com' => new AuthenticatedUser(1, 'john@doe.com', 'John', 'password', false),
        ]));

        $local_authorizer->verifyCredentials(['username' => 'john@doe.com', 'password' => 'password']);
    }

    public function testUserIsAuthenticated()
    {
        $local_authorizer = new LocalAuthorizer(new Repository([
            'john@doe.com' => new AuthenticatedUser(1, 'john@doe.com', 'John', 'password', true),
        ]));

        $user = $local_authorizer->verifyCredentials(['username' => 'john@doe.com', 'password' => 'password']);

        $this->assertSame(1, $user->getId());
    }
}
