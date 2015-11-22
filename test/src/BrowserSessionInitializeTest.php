<?php

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSession;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Base\BrowserSessionTestCase;
use ActiveCollab\Authentication\Test\Session\Session;

/**
 * @package ActiveCollab\Authentication\Test
 */
class BrowserSessionInitializeTest extends BrowserSessionTestCase
{
    /**
     * Test request cookies
     */
    public function testRequestCookie()
    {
        $this->assertEquals('123', $this->request->withCookieParams([
            'my_cookie' => '123',
        ])->getCookieParams()['my_cookie']);
    }

    /**
     * Test initialization skips when there's no session cookie
     */
    public function testInitializationSkipWhenTheresNoSessionCookie()
    {
        $this->assertNull((new BrowserSession($this->empty_users_repository, $this->empty_sessions_repository))->initialize($this->request));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidSession
     */
    public function testExceptionWhenSessionIsNotValid()
    {
        (new BrowserSession($this->empty_users_repository, $this->empty_sessions_repository))->initialize($this->request->withCookieParams([
            'sessid' => 'not a valid session ID',
        ]));
    }

    /**
     * Test if we get authetncated user when we use a good token
     */
    public function testAuthenticationWithGoodSessionId()
    {
        $test_session_id = 's123';

        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123')
        ]);

        $session_repository = new SessionRepository([
            $test_session_id => new Session($test_session_id, 'ilija.studen@activecollab.com'),
        ]);

        $user = (new BrowserSession($user_repository, $session_repository))->initialize($this->request->withCookieParams([
            'sessid' => 's123',
        ]));

        $this->assertInstanceOf(AuthenticatedUser::class, $user);
    }

    /**
     * Test if we get authetncated user when we use a good token
     */
    public function testAuthenticationWithGoodSessionIdAlsoSetsSession()
    {
        $test_session_id = 's123';

        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        $session_repository = new SessionRepository([
            $test_session_id => new Session($test_session_id, 'ilija.studen@activecollab.com'),
        ]);

        $session = null;

        $user = (new BrowserSession($user_repository, $session_repository))->initialize($this->request->withCookieParams([
            'sessid' => $test_session_id,
        ]), $session);

        $this->assertInstanceOf(AuthenticatedUser::class, $user);
        $this->assertInstanceOf(SessionInterface::class, $session);
    }

    /**
     * Test if session usage is recorded
     */
    public function testAuthenticationRecordsSessionUsage()
    {
        $test_session_id = 's123';

        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        $session_repository = new SessionRepository([
            $test_session_id => new Session($test_session_id, 'ilija.studen@activecollab.com'),
        ]);

        $this->assertSame(0, $session_repository->getUsageById($test_session_id));

        $user = (new BrowserSession($user_repository, $session_repository))->initialize($this->request->withCookieParams([
            'sessid' => $test_session_id,
        ]));

        $this->assertInstanceOf(AuthenticatedUser::class, $user);

        $this->assertSame(1, $session_repository->getUsageById($test_session_id));
    }
}
