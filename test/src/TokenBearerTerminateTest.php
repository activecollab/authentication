<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearerAdapter;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\TokenBearerTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;
use InvalidArgumentException;

class TokenBearerTerminateTest extends TokenBearerTestCase
{
    public function testTerminateNonSessionRaisesAnException()
    {
        $this->expectException(InvalidArgumentException::class);

        (new TokenBearerAdapter($this->empty_user_repository, $this->empty_token_repository))
            ->terminate(new Session('123', 'ilija.studen@activecollab.com'));
    }

    /**
     * Test if we can terminate a token.
     */
    public function testTerminateToken()
    {
        $test_token = '123';

        $token = new Token($test_token, '123');
        $user_repository = new UserRepository(
            [
                new AuthenticatedUser(
                    1,
                    'ilija.studen@activecollab.com',
                    'Ilija Studen',
                    '123'
                )
            ]
        );
        $token_repository = new TokenRepository(
            [
                $test_token => new Token(
                    $test_token,
                    'ilija.studen@activecollab.com'
                )
            ]
        );

        $token_bearer_adapter = new TokenBearerAdapter($user_repository, $token_repository);

        $this->assertInstanceOf(Token::class, $token_repository->getById($test_token));

        $termination_transport = $token_bearer_adapter->terminate($token);
        $this->assertInstanceOf(TransportInterface::class, $termination_transport);
        $this->assertNull($token_repository->getById($test_token));
    }
}
