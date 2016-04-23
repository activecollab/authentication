<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearer;
use ActiveCollab\Authentication\Authentication;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Authorizer\Authorizer;
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

        $this->authorizer = new Authorizer();
        $this->user_repository = new UserRepository([
            'john@doe.com' => new AuthenticatedUser(1, 'john@doe.com', 'John Doe', '123'),
        ]);
        $this->empty_user_repository = new UserRepository();
        $this->token_repository = new TokenRepository([
            '123' => new Token(123, 'john@doe.com'), ]
        );
        $this->empty_token_repository = new TokenRepository();
        $this->authenticated_user = new AuthenticatedUser(1, 'john@doe.com', 'John Doe', '123');
        $this->request = $this->request->withHeader('Authorization', 'Bearer 123');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Invalid object type provided
     */
    public function testForInvalidAdapterExceptionIsThrown()
    {
        new Authentication([new stdClass()], $this->authorizer);
    }

    /**
     * @expectedException ActiveCollab\Authentication\Exception\InvalidCredentialsException
     * @expectedExceptionMessage Invalid credentials provided
     */
    public function testForInvalidCredentialsExceptionIsThrown()
    {
        $authentication = new Authentication(
            [new TokenBearer($this->user_repository, $this->token_repository)],
            $this->authorizer
        );

        $authentication->authorize($this->request, ['username' => 'john@doe123.com']);
    }

    /**
     * @expectedException ActiveCollab\Authentication\Exception\InvalidTokenException
     * @expectedExceptionMessage Authorization token is not valid
     */
    public function testFailedAdapterInitializationThrowsException()
    {
        $token_bearer = new TokenBearer($this->empty_user_repository, $this->empty_token_repository);

        (new Authentication([$token_bearer], $this->authorizer))->initialize($this->request);
    }

    /**
     * @expectedException ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException
     * @expectedExceptionMessage You can not be authenticated with more than one authentication method
     */
    public function testMultipleAdapterSuccessfullyInitializedThrowsException()
    {
        $authentication = new Authentication(
            [new TokenBearer($this->user_repository, $this->token_repository), new TokenBearer($this->user_repository, $this->token_repository)],
            $this->authorizer
        );

        $authentication->initialize($this->request);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Authorizer object is not configured
     */
    public function testForNotConfiguredAuthorizerExceptionIsThrown()
    {
        $authentication = new Authentication([new TokenBearer($this->user_repository, $this->token_repository)], null);

        $authentication->authorize($this->request, ['username' => 'john@doe.com']);
    }

    public function testUserIsAuthorized()
    {
        $authentication = new Authentication(
            [new TokenBearer($this->user_repository, $this->token_repository)],
            $this->authorizer
        );

        $request = $authentication->initialize($this->request);

        $authentication_result = $authentication->authorize($request, ['username' => 'john@doe.com']);

        $this->assertInstanceOf(AuthenticationResultInterface::class, $authentication_result);
    }
}
