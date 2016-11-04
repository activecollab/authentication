<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearerAdapter;
use ActiveCollab\Authentication\Authentication;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Fixtures\Authorizer;
use ActiveCollab\Authentication\Test\TestCase\RequestResponseTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;
use stdClass;

class AuthenticationTest extends RequestResponseTestCase
{
    /**
     * @var Authorizer
     */
    private $authorizer;

    /**
     * @var UserRepository
     */
    private $user_repository;

    /**
     * @var UserRepository
     */
    private $empty_user_repository;

    /**
     * @var TokenRepository
     */
    private $token_repository;

    /**
     * @var TokenRepository
     */
    private $empty_token_repository;

    /**
     * @var AuthenticatedUser
     */
    private $authenticated_user;

    public function setUp()
    {
        parent::setUp();

        $authenticated_user = new AuthenticatedUser(1, 'john@doe.com', 'John Doe', '123');
        $this->authorizer = new Authorizer($authenticated_user);
        $this->user_repository = new UserRepository(['john@doe.com' => $authenticated_user]);
        $this->empty_user_repository = new UserRepository();
        $this->token_repository = new TokenRepository([
            '123' => new Token(123, 'john@doe.com'),
        ]);
        $this->empty_token_repository = new TokenRepository();
        $this->authenticated_user = new AuthenticatedUser(1, 'john@doe.com', 'John Doe', '123');
        $this->request = $this->request->withHeader('Authorization', 'Bearer 123');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid object type provided
     */
    public function testForInvalidAdapterExceptionIsThrown()
    {
        new Authentication([new stdClass()]);
    }

    public function testAdaptersNotInitializedReturnsNull()
    {
        $this->assertNull((new Authentication([]))->initialize($this->request));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidTokenException
     * @expectedExceptionMessage Authorization token is not valid
     */
    public function testFailedAdapterInitializationThrowsException()
    {
        $token_bearer = new TokenBearerAdapter($this->empty_user_repository, $this->empty_token_repository);

        (new Authentication([$token_bearer]))->initialize($this->request);
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException
     * @expectedExceptionMessage You can not be authenticated with more than one authentication method
     */
    public function testMultipleAdapterSuccessfullyInitializedThrowsException()
    {
        $authentication = new Authentication(
            [new TokenBearerAdapter($this->user_repository, $this->token_repository), new TokenBearerAdapter($this->user_repository, $this->token_repository)],
            $this->authorizer
        );

        $authentication->initialize($this->request);
    }

    public function testUserIsAuthorized()
    {
        $token_bearer = new TokenBearerAdapter($this->user_repository, $this->token_repository);

        $authentication = new Authentication([$token_bearer]);
        $authentication->initialize($this->request);

        $authentication_result = $authentication->authorize($this->authorizer, $token_bearer, ['username' => 'john@doe.com']);

        $this->assertInstanceOf(TransportInterface::class, $authentication_result);
    }
}
