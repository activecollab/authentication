<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\Adapter\TokenBearerAdapter;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Authentication;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\RequestResponseTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;
use ActiveCollab\Authentication\Token\TokenInterface;
use ActiveCollab\Cookies\Adapter\Adapter;
use ActiveCollab\Cookies\Cookies;
use ActiveCollab\Cookies\CookiesInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Test
 */
class AuthenticationMiddlewareTest extends RequestResponseTestCase
{
    /**
     * @var CookiesInterface
     */
    private $cookies;

    /**
     * @var AuthenticatedUserInterface
     */
    private $user;

    /**
     * @var RepositoryInterface
     */
    private $user_repository;

    /**
     * @var \ActiveCollab\Authentication\Session\RepositoryInterface
     */
    private $session_repository;

    /**
     * @var string
     */
    private $browser_session_cookie_name = 'test-session-cookie';

    /**
     * @var BrowserSessionAdapter
     */
    private $browser_session_adapter;

    /**
     * @var TokenRepository
     */
    private $token_repository;

    /**
     * @var TokenBearerAdapter
     */
    private $token_bearer_adapter;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->cookies = new Cookies(new Adapter());

        $this->user = new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123');
        $this->user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        $this->session_repository = new SessionRepository([new Session('my-session-id', 'ilija.studen@activecollab.com')]);
        $this->browser_session_adapter = new BrowserSessionAdapter($this->user_repository, $this->session_repository, $this->cookies, $this->browser_session_cookie_name);

        $this->token_repository = new TokenRepository(['awesome-token' => new Token('awesome-token', 'ilija.studen@activecollab.com')]);
        $this->token_bearer_adapter = new TokenBearerAdapter($this->user_repository, $this->token_repository);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Invalid authentication adapter provided
     */
    public function testExceptionIfInvalidAdaptersAreSet()
    {
        new Authentication([new \stdClass()]);
    }

    public function testMiddlewareAcceptsMultipleAdapters()
    {
        $middleware = new Authentication([$this->browser_session_adapter, $this->token_bearer_adapter]);

        $this->assertInternalType('array', $middleware->getAdapters());
        $this->assertCount(2, $middleware->getAdapters());
    }

    /**
     * Test that user is authenticated.
     */
    public function testBrowserSessionAuthentication()
    {
        /** @var ServerRequestInterface $request */
        /** @var ResponseInterface $response */
        list($request, $response) = $this->cookies->set($this->request, $this->response, $this->browser_session_cookie_name, 'my-session-id');

        $middleware = new Authentication([$this->browser_session_adapter]);

        /** @var ServerRequestInterface $modified_request */
        $modified_request = null;

        /** @var ResponseInterface $response */
        $response = call_user_func($middleware, $request, $response, function (ServerRequestInterface $request, ResponseInterface $response, callable $next = null) use (&$modified_request) {
            $modified_request = $request;

            if ($next) {
                $response = $next($request, $response);
            }

            return $response;
        });

        $this->assertInstanceOf(ServerRequestInterface::class, $modified_request);
        $this->assertInstanceOf(ResponseInterface::class, $response);

        // Test if authentication attributes are set
        $this->assertArrayHasKey('authentication_adapter', $modified_request->getAttributes());
        $this->assertArrayHasKey('authenticated_user', $modified_request->getAttributes());
        $this->assertArrayHasKey('authenticated_with', $modified_request->getAttributes());

        // Test if session cookie is set
        $set_cookie_header = $response->getHeaderLine('Set-Cookie');

        $this->assertNotEmpty($set_cookie_header);
        $this->assertContains($this->browser_session_cookie_name, $set_cookie_header);
        $this->assertContains('my-session-id', $set_cookie_header);

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $middleware->getAuthenticatedUser());
        $this->assertInstanceOf(SessionInterface::class, $middleware->getAuthenticatedWith());
    }

    public function testTokenBearerAuthentication()
    {
        /** @var ServerRequestInterface $request */
        $request = $this->request->withHeader('Authorization', 'Bearer awesome-token');

        $middleware = new Authentication([$this->token_bearer_adapter]);

        /** @var ServerRequestInterface $modified_request */
        $modified_request = null;

        $response = call_user_func($middleware, $request, $this->response, function (ServerRequestInterface $request, ResponseInterface $response, callable $next = null) use (&$modified_request) {
            $modified_request = $request;

            if ($next) {
                $response = $next($request, $response);
            }

            return $response;
        });

        $this->assertInstanceOf(ServerRequestInterface::class, $modified_request);
        $this->assertInstanceOf(ResponseInterface::class, $response);

        // Test if authentication attributes are set
        $this->assertArrayHasKey('authentication_adapter', $modified_request->getAttributes());
        $this->assertArrayHasKey('authenticated_user', $modified_request->getAttributes());
        $this->assertArrayHasKey('authenticated_with', $modified_request->getAttributes());

        // Test if session cookie is set
        $set_cookie_header = $response->getHeaderLine('Set-Cookie');
        $this->assertEmpty($set_cookie_header);

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $middleware->getAuthenticatedUser());
        $this->assertInstanceOf(TokenInterface::class, $middleware->getAuthenticatedWith());
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException
     * @expectedExceptionMessage You can not be authenticated with more than one authentication method
     */
    public function testExceptionOnMultipleIds()
    {
        /** @var ServerRequestInterface $request */
        /** @var ResponseInterface $response */
        list($request, $response) = $this->cookies->set($this->request, $this->response, $this->browser_session_cookie_name, 'my-session-id');

        /** @var ServerRequestInterface $request */
        $request = $request->withHeader('Authorization', 'Bearer awesome-token');

        call_user_func(new Authentication([$this->browser_session_adapter, $this->token_bearer_adapter]), $request, $response);
    }

    public function testOnAuthenticatedUserCallback()
    {
        /** @var ServerRequestInterface $request */
        $request = $this->request->withHeader('Authorization', 'Bearer awesome-token');

        $middleware = new Authentication([$this->token_bearer_adapter]);

        $callback_is_called = false;
        $middleware->setOnAuthenciatedUserChanged(function () use (&$callback_is_called) {
            $callback_is_called = true;
        });

        call_user_func($middleware, $request, $this->response);

        $this->assertTrue($callback_is_called);
    }
}
