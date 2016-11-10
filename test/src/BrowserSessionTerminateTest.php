<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\BrowserSessionTestCase;
use ActiveCollab\Authentication\Test\Token\Token;

/**
 * @package ActiveCollab\Authentication\Test
 */
class BrowserSessionTerminateTest extends BrowserSessionTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTerminteNonSessionRaisesAnException()
    {
        (new BrowserSessionAdapter($this->empty_user_repository, $this->empty_session_repository, $this->cookies))->terminate(new Token('123', 'ilija.studen@activecollab.com'));
    }

    /**
     * Test session termination.
     */
    public function testTerminateSession()
    {
        $test_session_id = 's123';

        $session = new Session($test_session_id, 123);
        $user_repository = new UserRepository([new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123')]);
        $session_repository = new SessionRepository([new Session($test_session_id, 'ilija.studen@activecollab.com')]);

        $browser_session_adapter = new BrowserSessionAdapter($user_repository, $session_repository, $this->cookies);

        $this->assertInstanceOf(Session::class, $session_repository->getById($test_session_id));

        $termination_transport = $browser_session_adapter->terminate($session);
        $this->assertInstanceOf(TransportInterface::class, $termination_transport);
        $this->assertNull($session_repository->getById($test_session_id));
    }
}
