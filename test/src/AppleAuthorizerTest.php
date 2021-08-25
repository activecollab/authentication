<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Apple\AppleClientInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Authorizer\AppleAuthorizer;
use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\TestCase\TestCase;
use Azimo\Apple\Auth\Exception\ValidationFailedException;
use Azimo\Apple\Auth\Struct\JwtPayload;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @package ActiveCollab\Authentication\Test
 */
class AppleAuthorizerTest extends TestCase
{
    private $client_id;

    /**
     * @var MockObject | AppleClientInterface
     */
    private $appleClient;

    /** @var RepositoryInterface| MockObject  */
    private $repository;


    public function setUp(): void
    {
        parent::setUp();

        $this->client_id = 'com.a51doo.ac';
        $this->appleClient = $this->createMock(AppleClientInterface::class);
        $this->repository = $this->createMock(RepositoryInterface::class);
    }

    /**
     * @dataProvider providerInvalidCredentials
     * @param array $credentials
     */
    public function testInvalidCredentialsThrowsException($credentials)
    {
        $this->expectException(InvalidAuthenticationRequestException::class);
        $this->expectExceptionMessage("Authentication request data not valid");

        $appleAuthorizer = new AppleAuthorizer(new Repository(), $this->appleClient, 'com.a51doo.ac');

        $appleAuthorizer->verifyCredentials($credentials);
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

    public function testExceptionIsThrownForInvalidToken()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");
        $this->appleClient
            ->method('verifyIdToken')
            ->willThrowException(new ValidationFailedException());

        $authorizer = new AppleAuthorizer(new Repository(), $this->appleClient, $this->client_id);

        $authorizer->verifyCredentials(['username'=>'someone@example.com', 'token'=>'eyABC.123']);
    }

    public function testExceptionIsThrownForNotAuthenticatedUser()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");

        $payload = new JwtPayload('https://appleid.apple.com', ['com.a51doo.ac'], new \DateTimeImmutable(), new \DateTimeImmutable(), '123', 'hAsH', 'someone@example.com', true, true, 1624615335, true);

        $this->appleClient
            ->method('verifyIdToken')
            ->willReturn($payload);
        $this->repository->method('findByUsername')
            ->willReturn(new AuthenticatedUser(1, 'someone@example.com', 'John', 'password', false));

        $authorizer = new AppleAuthorizer(
            $this->repository,
            $this->appleClient,
            $this->client_id
        );

        $authorizer->verifyCredentials(['username'=>'someone@example.com', 'token'=>'eyABC.123']);
    }

    public function testValidAuthenticatedUser()
    {
        $payload = new JwtPayload('https://appleid.apple.com', ['com.a51doo.ac'], new \DateTimeImmutable(), new \DateTimeImmutable(), '123', 'hAsH', 'someone@example.com', true, true, 1624615335, true);

        $this->appleClient
            ->method('verifyIdToken')
            ->willReturn($payload);
        $this->repository->method('findByUsername')
            ->willReturn(new AuthenticatedUser(1, 'someone@example.com', 'John', 'password', true));

        $authorizer = new AppleAuthorizer(
            $this->repository,
            $this->appleClient,
            $this->client_id
        );

        $result = $authorizer->verifyCredentials(['username'=>'someone@example.com', 'token'=>'eyABC.123']);
        $this->assertTrue($result instanceof AuthenticatedUser);
        $this->assertEquals('someone@example.com', $result->getEmail());
    }


    public function testExceptionIsThrownForInvalidJWT()
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");

        $payload = new JwtPayload('https://appleid.apple.com', ['com.something.ac'], new \DateTimeImmutable(), new \DateTimeImmutable(), '123', 'hAsH', 'someone@example.com', true, true, 1624615335, true);

        $this->appleClient
            ->method('verifyIdToken')
            ->willReturn($payload);
        $this->repository->method('findByUsername')
            ->willReturn(new AuthenticatedUser(1, 'someone@example.com', 'John', 'password', true));

        $authorizer = new AppleAuthorizer(
            $this->repository,
            $this->appleClient,
            $this->client_id
        );

        $authorizer->verifyCredentials(['username'=>'someone@example.com', 'token'=>'eyABC.123']);
    }
}
