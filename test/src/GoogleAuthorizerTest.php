<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Authorizer\GoogleAuthorizer;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\TestCase\TestCase;
use Exception;

/**
 * @package ActiveCollab\Authentication\Test
 */
class GoogleAuthorizerTest extends TestCase
{
    private $client_id;
    private $google_client;
    private $google_auth_login_ticket;

    public function setUp()
    {
        parent::setUp();

        $this->client_id = '123abc';
        $this->google_auth_login_ticket = $this->getMockBuilder('Google_Auth_LoginTicket')->disableOriginalConstructor()->getMock();
        $this->google_client = $this->getMockBuilder('Google_Client')->disableOriginalConstructor()->getMock();
    }

    /**
     * @dataProvider providerInvalidCredentials
     * @expectedException ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException
     * @expectedExceptionMessage Authentication request data not valid
     */
    public function testInvalidCredentialsThrowsException($credentials)
    {
        $google_authorizer = new GoogleAuthorizer(new Repository(), $this->google_client, $this->client_id);

        $google_authorizer->verifyCredentials($credentials);
    }

    public function providerInvalidCredentials()
    {
        return [
            [['username' => null, 'token' => null]],
            [['username' => '', 'token' => '']],
            [['username' => 'john@doe.com', 'token' => '']],
            [['username' => '', 'token' => '123abc']],
        ];
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unrecognized google_client
     */
    public function testExceptionIsThrownForInvalidAud()
    {
        $google_authorizer = new GoogleAuthorizer(new Repository(), $this->google_client, $this->client_id);

        $this->ensureTokenVerificationResult('123abacu', ['payload' => ['aud' => '111a']]);

        $google_authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Wrong issuer
     */
    public function testExceptionIsThrownForInvalidIss()
    {
        $google_authorizer = new GoogleAuthorizer(new Repository(), $this->google_client, $this->client_id);

        $this->ensureTokenVerificationResult('123abacu', [
            'payload' => ['aud' => '123abc', 'iss' => 'www.example.com'],
        ]);

        $google_authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Email is not verified by Google
     */
    public function testExceptionIsThrownForInvalidUsername()
    {
        $google_authorizer = new GoogleAuthorizer(new Repository(), $this->google_client, $this->client_id);

        $this->ensureTokenVerificationResult('123abacu', [
            'payload' => ['aud' => '123abc', 'iss' => 'accounts.google.com', 'email' => 'john123@doe.com'],
        ]);

        $google_authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);
    }

    /**
     * @expectedException ActiveCollab\Authentication\Exception\UserNotFoundException
     * @expectedExceptionMessage User not found
     */
    public function testExceptionIsThrownForNotFoundUser()
    {
        $google_authorizer = new GoogleAuthorizer(new Repository(), $this->google_client, $this->client_id);

        $payload = [
            'aud' => '123abc',
            'iss' => 'accounts.google.com',
            'email' => 'john@doe.com',
        ];
        $this->ensureTokenVerificationResult('123abacu', ['payload' => $payload]);

        $google_authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);
    }

    /**
     * @expectedException ActiveCollab\Authentication\Exception\UserNotFoundException
     * @expectedExceptionMessage User not found
     */
    public function testExceptionIsThrownForNotAuthenticatedUser()
    {
        $google_authorizer = new GoogleAuthorizer(new Repository([
                'john@doe.com' => new AuthenticatedUser(1, 'john@doe.com', 'John', 'password', false),
            ]),
            $this->google_client,
            $this->client_id
        );

        $payload = [
            'aud' => '123abc',
            'iss' => 'accounts.google.com',
            'email' => 'john@doe.com',
        ];
        $this->ensureTokenVerificationResult('123abacu', ['payload' => $payload]);

        $google_authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);
    }

    public function testUserIsFoundAndVerified()
    {
        $google_authorizer = new GoogleAuthorizer(new Repository([
                'john@doe.com' => new AuthenticatedUser(1, 'john@doe.com', 'John', 'password', true),
            ]),
            $this->google_client,
            $this->client_id
        );

        $payload = [
            'aud' => '123abc',
            'iss' => 'accounts.google.com',
            'email' => 'john@doe.com',
        ];
        $this->ensureTokenVerificationResult('123abacu', ['payload' => $payload]);

        $user = $google_authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);

        $this->assertSame(1, $user->getId());
    }

    private function ensureTokenVerificationResult($token, $results)
    {
        $this
            ->google_client
            ->expects($this->once())
            ->method('verifyIdToken')
            ->with($token)
            ->will($this->returnValue($this->google_auth_login_ticket));

        $this
            ->google_auth_login_ticket
            ->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue($results));
    }

    private function ensureTokenVerificationFailed($token, $error)
    {
        $this
            ->google_client
            ->expects($this->once())
            ->method('verifyIdToken')
            ->with($token)
            ->will($this->throwException(new Exception($error)));
    }
}
