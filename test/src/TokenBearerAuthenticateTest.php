<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearerAdapter;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\TestCase\TokenBearerTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;
use ActiveCollab\Authentication\Token\TokenInterface;

class TokenBearerAuthenticateTest extends TokenBearerTestCase
{
    /**
     * Test if good credentials authenticate the user.
     */
    public function testUserIsAuthenticated()
    {
        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);
        $token_repository = new TokenRepository([
            'ilija.studen@activecollab.com' => 'awesome-token',
        ]);
        $user = new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123');

        $result = (new TokenBearerAdapter($user_repository, $token_repository))->authenticate($user, []);

        $this->assertInstanceOf(TokenInterface::class, $result);
    }

    /**
     * Test if good credentials authenticate the user.
     */
    public function testCredentialsArePassedOnToAdapter()
    {
        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);
        $token_repository = new TokenRepository([
            'ilija.studen@activecollab.com' => 'awesome-token',
        ]);
        $user = new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123');

        /** @var Token $result */
        $result = (new TokenBearerAdapter($user_repository, $token_repository))->authenticate($user, [
            'extra_attribute' => '123',
        ]);

        $this->assertInstanceOf(TokenInterface::class, $result);
        $this->assertSame('123', $result->getExtraAttribute());
    }
}
