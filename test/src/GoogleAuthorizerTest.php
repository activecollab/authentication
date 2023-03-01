<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Authorizer\GoogleAuthorizer;
use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\TestCase\TestCase;;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

/**
 * @package ActiveCollab\Authentication\Test
 */
class GoogleAuthorizerTest extends TestCase
{
    private string $client_id;
    private MockObject $google_client;
    private MockObject $google_auth_login_ticket;

    public function setUp(): void
    {
        parent::setUp();

        $this->client_id = '123abc';
        $this->google_auth_login_ticket = $this->getMockBuilder('Google_Auth_LoginTicket')
            ->disableOriginalConstructor()
            ->getMock();
        $this->google_client = $this->getMockBuilder('Google_Client')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @dataProvider providerInvalidCredentials
     */
    public function testInvalidCredentialsThrowsException(array $credentials)
    {
        $this->expectException(InvalidAuthenticationRequestException::class);
        $this->expectExceptionMessage("Authentication request data not valid");

        $google_authorizer = new GoogleAuthorizer(
            new Repository(),
            $this->google_client,
            $this->client_id
        );

        $google_authorizer->verifyCredentials($credentials);
    }

    public function providerInvalidCredentials(): array
    {
        return [
            [['username' => null, 'token' => null]],
            [['username' => '', 'token' => '']],
            [['username' => 'john@doe.com', 'token' => '']],
        ];
    }

    public function testExceptionIsThrownForInvalidAud()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Unrecognized google_client");

        $this->google_client
            ->method('verifyIdToken')
            ->willReturn(['aud' => '111a']);

        $google_authorizer = new GoogleAuthorizer(
            new Repository(),
            $this->google_client,
            $this->client_id
        );

        $this->ensureTokenVerificationResult('123abacu', ['aud' => '111a']);

        $google_authorizer->verifyCredentials([
            'token' => '123abacu',
            'username' => 'john@doe.com',
        ]);
    }

    public function testExceptionIsThrownForInvalidIss()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Wrong issuer");

        $this->google_client
            ->method('verifyIdToken')
            ->willReturn(['aud' => '123abc', 'iss' => 'www.example.com']);

        $google_authorizer = new GoogleAuthorizer(
            new Repository(),
            $this->google_client,
            $this->client_id
        );

        $this->ensureTokenVerificationResult(
            '123abacu',
            [
                'aud' => '123abc',
                'iss' => 'www.example.com',
            ],
        );

        $google_authorizer->verifyCredentials([
            'token' => '123abacu',
            'username' => 'john@doe.com'
        ]);
    }

    public function testExceptionIsThrownForInvalidUsername()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Email is not verified by Google");

        $this->google_client
            ->method('verifyIdToken')
            ->willReturn([
                'aud' => '123abc',
                'iss' => 'accounts.google.com',
                'email' => 'john123@doe.com',
            ]);

        $google_authorizer = new GoogleAuthorizer(
            new Repository(),
            $this->google_client,
            $this->client_id
        );

        $this->ensureTokenVerificationResult(
            '123abacu',
            [
                'aud' => '123abc',
                'iss' => 'accounts.google.com',
                'email' => 'john123@doe.com',
            ],
        );

        $google_authorizer->verifyCredentials([
            'token' => '123abacu',
            'username' => 'john@doe.com',
        ]);
    }

    public function testExceptionIsThrownForNotFoundUser()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");

        $payload = [
            'aud' => '123abc',
            'iss' => 'accounts.google.com',
            'email' => 'john@doe.com',
        ];

        $this->google_client
            ->method('verifyIdToken')
            ->willReturn($payload);

        $google_authorizer = new GoogleAuthorizer(
            new Repository(),
            $this->google_client,
            $this->client_id
        );

        $this->ensureTokenVerificationResult('123abacu', $payload);

        $google_authorizer->verifyCredentials([
            'token' => '123abacu',
            'username' => 'john@doe.com'
        ]);
    }

    public function testExceptionIsThrownForNotAuthenticatedUser()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");

        $payload = [
            'aud' => '123abc',
            'iss' => 'accounts.google.com',
            'email' => 'john@doe.com',
        ];

        $this->google_client
            ->method('verifyIdToken')
            ->willReturn($payload);

        $google_authorizer = new GoogleAuthorizer(
            new Repository([
                'john@doe.com' => new AuthenticatedUser(
                    1,
                    'john@doe.com',
                    'John',
                    'password',
                    false
                ),
            ]),
            $this->google_client,
            $this->client_id
        );

        $this->ensureTokenVerificationResult('123abacu', $payload);

        $google_authorizer->verifyCredentials([
            'token' => '123abacu',
            'username' => 'john@doe.com',
        ]);
    }

    /**
     * @dataProvider providePayload
     */
    public function testUserIsFoundAndVerified(?string $username, array $payload)
    {
        $this->google_client
            ->method('verifyIdToken')
            ->willReturn($payload);

        $google_authorizer = new GoogleAuthorizer(new Repository([
            'john@doe.com' => new AuthenticatedUser(
                1,
                'john@doe.com',
                'John',
                'password',
                true
            ),
        ]),
            $this->google_client,
            $this->client_id
        );

        $this->ensureTokenVerificationResult('123abacu', $payload);

        $user = $google_authorizer->verifyCredentials([
            'token' => '123abacu',
            'username' => 'john@doe.com',
        ]);

        $this->assertSame(1, $user->getId());
        $this->assertSame($payload, $google_authorizer->getUserProfile());
    }

    private function ensureTokenVerificationResult(string $token, ?array $results): void
    {
        $this
            ->google_client
            ->expects($this->exactly(2))
            ->method('verifyIdToken')
            ->with($token)
            ->will($this->returnValue($this->google_auth_login_ticket));

        $this->assertSame($results, $this->google_client->verifyIdToken($token));
    }

    public function providePayload(): array
    {
        return [
            [
                'john@doe.com',
                [
                    'aud' => '123abc',
                    'iss' => 'accounts.google.com',
                    'email' => 'john@doe.com',
                ],
            ],
            [
                null,
                [
                    'aud' => '123abc',
                    'iss' => 'accounts.google.com',
                    'email' => 'john@doe.com',
                ],
            ],
            [
                'johnusername@doe.com',
                [
                    'aud' => '123abc',
                    'iss' => 'accounts.google.com',
                    'email' => 'john@doe.com',
                ],
            ],
        ];
    }
}
