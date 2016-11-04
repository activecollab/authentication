<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\BrowserSessionTestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class BrowserSessionInitializeTest extends BrowserSessionTestCase
{
    /**
     * Test request cookies.
     */
    public function testRequestCookie()
    {
        $this->setCookie('my_cookie', '123');
        $this->assertEquals('123', $this->cookies->get($this->request, 'my_cookie'));
    }

    /**
     * Test initialization skips when there's no session cookie.
     */
    public function testInitializationSkipWhenTheresNoSessionCookie()
    {
        $this->assertNull((new BrowserSessionAdapter($this->empty_user_repository, $this->empty_session_repository, $this->cookies))->initialize($this->request));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidSessionException
     */
    public function testExceptionWhenSessionIsNotValid()
    {
        $this->setCookie('sessid', 'not a valid session ID');

        (new BrowserSessionAdapter($this->empty_user_repository, $this->empty_session_repository, $this->cookies))->initialize($this->request);
    }

    /**
     * Test if we get authenticated user when we use a good token.
     */
    public function testAuthenticationWithGoodSessionId()
    {
        $test_session_id = 's123';

        $user_repository = new UserRepository([new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123')]);
        $session_repository = new SessionRepository([new Session($test_session_id, 'ilija.studen@activecollab.com')]);

        $this->setCookie('sessid', $test_session_id);

        $results = (new BrowserSessionAdapter($user_repository, $session_repository, $this->cookies))->initialize($this->request);

        $this->assertInstanceOf(AuthenticatedUser::class, $results['authenticated_user']);
        $this->assertInstanceOf(Session::class, $results['authenticated_with']);
    }

    /**
     * Test if session usage is recorded.
     */
    public function testAuthenticationRecordsSessionUsage()
    {
        $test_session_id = 's123';

        $user_repository = new UserRepository([new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123')]);
        $session_repository = new SessionRepository([new Session($test_session_id, 'ilija.studen@activecollab.com')]);

        $this->setCookie('sessid', $test_session_id);

        $this->assertSame(0, $session_repository->getUsageById($test_session_id));

        $results = (new BrowserSessionAdapter($user_repository, $session_repository, $this->cookies))->initialize($this->request);

        $this->assertInstanceOf(AuthenticatedUser::class, $results['authenticated_user']);
        $this->assertInstanceOf(Session::class, $results['authenticated_with']);

        $this->assertSame(1, $session_repository->getUsageById($test_session_id));
    }

    /**
     * Test session ttl expiration
     */
    public function testAuthenticationSessionTtlDuration() 
    {
        $test_session_id = 's123';

        $user_repository = new UserRepository([new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123')]);
        $session = new Session($test_session_id, 'ilija.studen@activecollab.com');

        // Check Default
        $this->assertEquals(Session::SESSION_TTL, $session->getSessionTtl());

        // Check if extended
        $session->setIsExtendedSession(true);
        $this->assertEquals(Session::EXTENDED_SESSION_TTL, $session->getSessionTtl());
    }
}
