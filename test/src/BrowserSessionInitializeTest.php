<?php

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSession;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\Base\BrowserSessionTestCase;

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
        $repository = new Repository([], [], [
            's123' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        $user = (new BrowserSession($repository, $this->empty_sessions_repository))->initialize($this->request->withCookieParams([
            'sessid' => 's123',
        ]));

        $this->assertInstanceOf(AuthenticatedUser::class, $user);
    }

    /**
     * Test if session usage is recorded
     */
    public function testAuthenticationRecordsSessionUsage()
    {
        $repository = new Repository([], [], [
            's123' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        $this->assertSame(0, $repository->getSessionUsage('s123'));

        $user = (new BrowserSession($repository, $this->empty_sessions_repository))->initialize($this->request->withCookieParams([
            'sessid' => 's123',
        ]));

        $this->assertInstanceOf(AuthenticatedUser::class, $user);

        $this->assertSame(1, $repository->getSessionUsage('s123'));
    }
}
