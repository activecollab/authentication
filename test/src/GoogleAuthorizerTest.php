<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Authorizer\GoogleAuthorizer;
use Exception;
use PHPUnit_Framework_TestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class GoogleAuthorizerTest extends PHPUnit_Framework_TestCase
{
    private $client_id;
    private $google_client;
    private $google_auth_login_ticket;
    private $authenticator;

    public function setUp()
    {
        parent::setUp();

        $this->client_id = '123abc';
        $this->google_auth_login_ticket = $this->getMockBuilder('Google_Auth_LoginTicket')->disableOriginalConstructor()->getMock();
        $this->google_client = $this->getMockBuilder('Google_Client')->disableOriginalConstructor()->getMock();
        $this->authorizer = new GoogleAuthorizer($this->google_client, $this->client_id);
    }

    public function testVerifyTokenThrowsException()
    {
        $this->ensureTokenVerificationFailed('123abacu', 'Google Error.');

        $results = $this->authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);

        $this->assertTrue($results['is_error']);
        $this->assertSame('Google Error.', $results['payload']);
    }

    public function testExceptionIsThrownForInvalidAud()
    {
        $this->ensureTokenVerificationResult('123abacu', ['payload' => ['aud' => '111a']]);

        $results = $this->authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);

        $this->assertTrue($results['is_error']);
        $this->assertSame('Unrecognized google_client.', $results['payload']);
    }

    public function testExceptionIsThrownForInvalidIss()
    {
        $this->ensureTokenVerificationResult('123abacu', [
            'payload' => ['aud' => '123abc', 'iss' => 'www.example.com'],
        ]);

        $results = $this->authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);

        $this->assertTrue($results['is_error']);
        $this->assertSame('Wrong issuer.', $results['payload']);
    }

    public function testExceptionIsThrownForInvalidUsername()
    {
        $this->ensureTokenVerificationResult('123abacu', [
            'payload' => ['aud' => '123abc', 'iss' => 'accounts.google.com', 'email' => 'john123@doe.com'],
        ]);

        $results = $this->authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);

        $this->assertTrue($results['is_error']);
        $this->assertSame('Email is not verified by Google.', $results['payload']);
    }

    public function testTokenIsVerified()
    {
        $payload = [
            'aud' => '123abc',
            'iss' => 'accounts.google.com',
            'email' => 'john@doe.com',
        ];
        $this->ensureTokenVerificationResult('123abacu', ['payload' => $payload]);

        $results = $this->authorizer->verifyCredentials(['token' => '123abacu', 'username' => 'john@doe.com']);

        $this->assertFalse($results['is_error']);
        $this->assertEquals($payload, $results['payload']);
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
