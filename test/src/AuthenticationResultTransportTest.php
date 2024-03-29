<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearerAdapter;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransport;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\TestCase\TestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;

class AuthenticationResultTransportTest extends TestCase
{
    public function testIsEmpty()
    {
        $empty_transport = new AuthenticationTransport(
            new TokenBearerAdapter(
                new UserRepository(),
                new TokenRepository(),
            ),
            null,
            null,
        );
        $this->assertTrue($empty_transport->isEmpty());

        $not_empty_transport = new AuthenticationTransport(
            new TokenBearerAdapter(
                new UserRepository(),
                new TokenRepository()),
            new AuthenticatedUser(
                1,
                'test@example.com',
                'John Doe',
                'secret',
            ),
            new Token('123', '1'),
        );
        $this->assertFalse($not_empty_transport->isEmpty());
    }
}
