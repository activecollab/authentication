<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearerAdapter;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransport;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\TestCase\RequestResponseTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;

/**
 * @package ActiveCollab\Authentication\Test
 */
class TransportTest extends RequestResponseTestCase
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

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->assertArrayNotHasKey('authentication_adapter', $this->request->getAttributes());
        $this->assertArrayNotHasKey('authenticated_user', $this->request->getAttributes());
        $this->assertArrayNotHasKey('authenticated_with', $this->request->getAttributes());

        $this->user_repository = new UserRepository();
        $this->user = new AuthenticatedUser(1, 'test@example.com', 'John Doe', 'secret');

        $this->token_repository = new TokenRepository();
        $this->token = new Token('123', 1);
        $this->token_bearer_adapter = new TokenBearerAdapter($this->user_repository, $this->token_repository);
    }

    public function testTransportedPayloadManipulation()
    {
        $transport = new AuthenticationTransport($this->token_bearer_adapter, $this->user, $this->token);
        $this->assertNull($transport->getPayload());

        $new_payload = [1, 2, 3];

        $transport->setPayload($new_payload);
        $this->assertSame($new_payload, $transport->getPayload());
    }

    public function testTransportCanStorePayload()
    {
        $payload = [1, 2, 3];

        $transport = new AuthenticationTransport($this->token_bearer_adapter, $this->user, $this->token, $payload);

        $this->assertSame($payload, $transport->getPayload());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Empty authentication transport cannot be applied
     */
    public function testEmptyTransportCantBeApplied()
    {
        $transport = new AuthenticationTransport($this->token_bearer_adapter);
        $this->assertTrue($transport->isEmpty());

        $transport->applyTo($this->request, $this->response);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Authentication transport already applied
     */
    public function testTransportCantBeAppliedTwice()
    {
        $transport = new AuthenticationTransport($this->token_bearer_adapter, $this->user, $this->token);
        $this->assertFalse($transport->isApplied());

        $transport->applyTo($this->request, $this->response);

        $this->assertTrue($transport->isApplied());

        $transport->applyTo($this->request, $this->response);
    }
}
