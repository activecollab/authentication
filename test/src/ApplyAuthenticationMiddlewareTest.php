<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authorization\AuthorizationTransport;
use ActiveCollab\Authentication\Middleware\ApplyAuthenticationMiddleware;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\RequestResponseTestCase;
use ActiveCollab\Cookies\Adapter\Adapter;
use ActiveCollab\Cookies\Cookies;
use ActiveCollab\Cookies\CookiesInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Test
 */
class ApplyAuthenticationMiddlewareTest extends RequestResponseTestCase
{
    /**
     * @var CookiesInterface
     */
    private $cookies;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->cookies = new Cookies(new Adapter());
    }

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

        $session_cookie_name = 'test-session-cookie';

        $session_adapter = new BrowserSessionAdapter($user_repository, $session_repository, $this->cookies, $session_cookie_name);
        $session = $session_adapter->authenticate($user, []);

        /** @var ServerRequestInterface $request */
        $request = $this->request->withAttribute('test_transport', new AuthorizationTransport($session_adapter, $user, $session, [1, 2, 3]));

        /** @var ResponseInterface $response */
        $response = call_user_func(new ApplyAuthenticationMiddleware('test_transport'), $request, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $set_cookie_header = $response->getHeaderLine('Set-Cookie');

        $this->assertNotEmpty($set_cookie_header);
        $this->assertContains($session_cookie_name, $set_cookie_header);
        $this->assertContains('my-session-id', $set_cookie_header);
    }
}
