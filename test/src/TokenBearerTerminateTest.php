<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearer;
use ActiveCollab\Authentication\Exception\InvalidToken;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Base\TokenBearerTestCase;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;
use ActiveCollab\Authentication\Token\TokenInterface;

/**
 * @package ActiveCollab\Authentication\Test
 */
class TokenBearerTerminateTest extends TokenBearerTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTerminteNonSessionRaisesAnException()
    {
        (new TokenBearer($this->empty_users_repository, $this->empty_tokens_repository))->terminate(new Session('123', 'ilija.studen@activecollab.com'));
    }

    /**
     * Test if we can terminate a token.
     */
    public function testTerminateToken()
    {
        $test_token = '123';

        $user_repository = new UserRepository([new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123')]);
        $token_repository = new TokenRepository([$test_token => new Token($test_token, 'ilija.studen@activecollab.com')]);

        $token_bearer_adapter = new TokenBearer($user_repository, $token_repository);

        // ---------------------------------------------------
        //  Initialize
        // ---------------------------------------------------

        $token = null;
        $user = $token_bearer_adapter->initialize($this->request->withHeader('Authorization', "Bearer {$test_token}"), $token);

        $this->assertInstanceOf(AuthenticatedUser::class, $user);
        $this->assertInstanceOf(TokenInterface::class, $token);

        // ---------------------------------------------------
        //  Terminate
        // ---------------------------------------------------

        $token_bearer_adapter->terminate($token);

        $this->setExpectedException(InvalidToken::class);
        $token_bearer_adapter->initialize($this->request->withHeader('Authorization', "Bearer {$test_token}"));
    }
}
