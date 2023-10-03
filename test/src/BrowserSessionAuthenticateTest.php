<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\BrowserSessionTestCase;

class BrowserSessionAuthenticateTest extends BrowserSessionTestCase
{
    /**
     * Test that user is authenticated.
     */
    public function testUserIsAuthenticated()
    {
        $user = new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123');
        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);
        $session_repository = new SessionRepository([new Session('my-session-id', 'ilija.studen@activecollab.com')]);

        $result = (new BrowserSessionAdapter($user_repository, $session_repository, $this->cookies))->authenticate($user, []);

        $this->assertInstanceOf(AuthenticationResultInterface::class, $result);
        $this->assertInstanceOf(SessionInterface::class, $result);
    }

    /**
     * Test to make sure that sessions repository fixture does not set session as extended by default.
     */
    public function testSessionIsNotExtendedByDefault()
    {
        $user = new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123');
        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);
        $session_repository = new SessionRepository();

        /** @var Session $result */
        $result = (new BrowserSessionAdapter($user_repository, $session_repository, $this->cookies))->authenticate($user, []);

        $this->assertInstanceOf(AuthenticationResultInterface::class, $result);
        $this->assertInstanceOf(SessionInterface::class, $result);
        $this->assertFalse($result->getIsExtendedSession());
    }

    /**
     * Test if session can be extended via credential attribute (basically, a test if we can extend session via credential attribute).
     */
    public function testSessionCanBeExtendedViaCredentialAttribute()
    {
        $user = new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123');
        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);
        $session_repository = new SessionRepository();

        /** @var Session $result */
        $result = (new BrowserSessionAdapter($user_repository, $session_repository, $this->cookies))->authenticate($user, ['remember' => true]);

        $this->assertInstanceOf(AuthenticationResultInterface::class, $result);
        $this->assertInstanceOf(SessionInterface::class, $result);
        $this->assertTrue($result->getIsExtendedSession());
    }
}
