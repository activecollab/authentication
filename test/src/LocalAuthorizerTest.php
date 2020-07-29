<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use ActiveCollab\Authentication\Authorizer\LocalAuthorizer;
use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;
use ActiveCollab\Authentication\Exception\InvalidPasswordException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

class LocalAuthorizerTest extends TestCase
{
    /**
     * @dataProvider providerInvalidCredentials
     * @param array $credentials
     */
    public function testInvalidCredentialsThrowsException($credentials)
    {
        $this->expectException(InvalidAuthenticationRequestException::class);
        $this->expectExceptionMessage("Authentication request data not valid");
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
     * @dataProvider providerInvalidAlphaNumUsername
     * @param array $username
     */
    public function testInvalidAlphaNumUsernameThrowsException($username)
    {
        $this->expectException(InvalidAuthenticationRequestException::class);
        $this->expectExceptionMessage("Authentication request data not valid");

        $local_authorizer = new LocalAuthorizer(
            new Repository(),
            AuthorizerInterface::USERNAME_FORMAT_ALPHANUM
        );

        $local_authorizer->verifyCredentials(
            [
                'username' => $username,
                'password' => 'Easy to remember, Hard to guess',
            ]
        );
    }

    public function providerInvalidAlphaNumUsername()
    {
        return [
            ['username' => null],
            ['username' => ''],
            ['username' => 'Invalid Username'],
            ['username' => 'not_a_username'],
            ['username' => 'coolperson@example.com'],
        ];
    }

    /**
     * @dataProvider providerInvalidEmailUsername
     * @param array $username
     */
    public function testInvalidEmailUsernameThrowsException($username)
    {
        $this->expectException(InvalidAuthenticationRequestException::class);
        $this->expectExceptionMessage("Authentication request data not valid");

        $local_authorizer = new LocalAuthorizer(
            new Repository(),
            AuthorizerInterface::USERNAME_FORMAT_EMAIL
        );

        $local_authorizer->verifyCredentials([
            'username' => $username,
            'password' => 'Easy to remember, Hard to guess',
        ]);
    }

    public function providerInvalidEmailUsername()
    {
        return [
            ['username' => null],
            ['username' => ''],
            ['username' => 'Invalid Username'],
            ['username' => 'Not a valid Username'],
            ['username' => 'not_a_username'],
        ];
    }

    public function testUserNotFoundThrowsException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");

        $local_authorizer = new LocalAuthorizer(new Repository());

        $local_authorizer->verifyCredentials(
            [
                'username' => 'user',
                'password' => 'password'
            ]
        );
    }

    public function testInvalidPasswordThrowsException()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage("Password not valid");

        $local_authorizer = new LocalAuthorizer(
            new Repository(
                [
                    'john@doe.com' => new AuthenticatedUser(
                        1,
                        'john@doe.com',
                        'John',
                        'invalid_password',
                        true
                    ),
                ]
            )
        );

        $local_authorizer->verifyCredentials(['username' => 'john@doe.com', 'password' => 'password']);
    }

    public function testUserCanNotAuthenticateThrowsException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");

        $local_authorizer = new LocalAuthorizer(new Repository([
            'john@doe.com' => new AuthenticatedUser(1, 'john@doe.com', 'John', 'password', false),
        ]));

        $local_authorizer->verifyCredentials(['username' => 'john@doe.com', 'password' => 'password']);
    }

    public function testUserIsAuthenticated()
    {
        $local_authorizer = new LocalAuthorizer(
            new Repository(
                [
                    'john@doe.com' => new AuthenticatedUser(
                        1,
                        'johndoe',
                        'John',
                        'password',
                        true
                    ),
                ]
            )
        );

        $user = $local_authorizer->verifyCredentials(['username' => 'johndoe', 'password' => 'password']);

        $this->assertSame(1, $user->getId());
    }

    public function testUserWithAlphanumUsernameIsAuthenticated()
    {
        $local_authorizer = new LocalAuthorizer(
            new Repository(
                [
                    'john@doe.com' => new AuthenticatedUser(
                        1,
                        'JohnDoe1983',
                        'John',
                        'password',
                        true
                    ),
                ]
            ),
            AuthorizerInterface::USERNAME_FORMAT_ALPHANUM
        );

        $user = $local_authorizer->verifyCredentials(
            [
                'username' => 'JohnDoe1983',
                'password' => 'password'
            ]
        );

        $this->assertSame(1, $user->getId());
    }

    public function testUserWithEmailUsernameIsAuthenticated()
    {
        $local_authorizer = new LocalAuthorizer(
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
            ),
            AuthorizerInterface::USERNAME_FORMAT_EMAIL
        );

        $user = $local_authorizer->verifyCredentials(
            [
                'username' => 'john@doe.com',
                'password' => 'password'
            ]
        );

        $this->assertSame(1, $user->getId());
    }
}
