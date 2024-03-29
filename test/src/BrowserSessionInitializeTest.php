<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\AuthenticationResult\Transport\CleanUp\CleanUpTransportInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Transport;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\BrowserSessionTestCase;

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

    public function testInvalidSessionRequiresCleanUp()
    {
        $this->setCookie('sessid', 'not a valid session ID');

        $result = (new BrowserSessionAdapter($this->empty_user_repository, $this->empty_session_repository, $this->cookies))->initialize($this->request);
        $this->assertInstanceOf(CleanUpTransportInterface::class, $result);
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

        $this->assertInstanceOf(Transport::class, $results);

        $this->assertInstanceOf(AuthenticatedUser::class, $results->getAuthenticatedUser());
        $this->assertInstanceOf(Session::class, $results->getAuthenticatedWith());
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

        $this->assertInstanceOf(Transport::class, $results);

        $this->assertInstanceOf(AuthenticatedUser::class, $results->getAuthenticatedUser());
        $this->assertInstanceOf(Session::class, $results->getAuthenticatedWith());

        $this->assertSame(1, $session_repository->getUsageById($test_session_id));
    }

    /**
     * Test session ttl expiration.
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
