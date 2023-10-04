<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearerAdapter;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransport;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\TestCase\RequestResponseTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TokenBearerAdapterApplyTest extends RequestResponseTestCase
{
    /**
     * @var UserRepository
     */
    private $user_repository;

    /**
     * @var AuthenticatedUserInterface
     */
    private $user;

    /**
     * @var TokenRepository
     */
    private $token_repository;

    /**
     * @var Token
     */
    private $token;

    /**
     * @var TokenBearerAdapter
     */
    private $token_bearer_adapter;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertArrayNotHasKey('authentication_adapter', $this->request->getAttributes());
        $this->assertArrayNotHasKey('authenticated_user', $this->request->getAttributes());
        $this->assertArrayNotHasKey('authenticated_with', $this->request->getAttributes());

        $this->user_repository = new UserRepository();
        $this->user = new AuthenticatedUser(1, 'test@example.com', 'John Doe', 'secret');

        $this->token_repository = new TokenRepository();
        $this->token = new Token('123', '1');
        $this->token_bearer_adapter = new TokenBearerAdapter($this->user_repository, $this->token_repository);
    }

    public function testTokenBearerAdapterReturnsRequestAndResponse()
    {
        $transport = new AuthenticationTransport($this->token_bearer_adapter, $this->user, $this->token);

        list($request, $response) = $this->token_bearer_adapter->applyTo($this->request, $this->response, $transport);
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
