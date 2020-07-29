<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authorization\AuthorizationTransport;
use ActiveCollab\Authentication\Middleware\ApplyAuthenticationMiddleware;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\RequestResponseTestCase;
use ActiveCollab\Cookies\Cookies;
use ActiveCollab\Cookies\CookiesInterface;
use ActiveCollab\ValueContainer\Request\RequestValueContainer;
use ActiveCollab\ValueContainer\ValueContainer;
use ActiveCollab\ValueContainer\ValueContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\ResponseFactory;

class ApplyAuthenticationMiddlewareTest extends RequestResponseTestCase
{
    /**
     * @var CookiesInterface
     */
    private $cookies;

    /**
     * @var ValueContainerInterface
     */
    private $value_container;

    public function setUp(): void
    {
        parent::setUp();

        $this->cookies = new Cookies();
        $this->value_container = new ValueContainer();
    }

    /**
     * Test if authentication is applied based on request attribute.
     */
    public function testUserIsAuthenticated()
    {
        $user = new AuthenticatedUser(
            1,
            'ilija.studen@activecollab.com',
            'Ilija Studen',
            '123'
        );

        $userRepository = new UserRepository(
            [
                'ilija.studen@activecollab.com' => new AuthenticatedUser(
                    1,
                    'ilija.studen@activecollab.com',
                    'Ilija Studen',
                    '123'
                ),
            ]
        );

        $sessionRepository = new SessionRepository(
            [
                new Session(
                    'my-session-id',
                    'ilija.studen@activecollab.com'
                ),
            ]
        );

        $sessionCookieName = 'test-session-cookie';

        $sessionAdapter = new BrowserSessionAdapter(
            $userRepository,
            $sessionRepository,
            $this->cookies,
            $sessionCookieName
        );
        $session = $sessionAdapter->authenticate($user, []);

        /** @var ServerRequestInterface $request */
        $request = $this->request->withAttribute(
            'test_transport',
            new AuthorizationTransport(
                $sessionAdapter,
                $user,
                $session,
                [
                    1,
                    2,
                    3,
                ]
            ));

        $middleware = new ApplyAuthenticationMiddleware(new RequestValueContainer('test_transport'));
        $this->assertFalse($middleware->applyOnExit());

        /** @var ResponseInterface $response */
        $response = call_user_func($middleware, $request, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $setCookieHeader = $response->getHeaderLine('Set-Cookie');

        $this->assertNotEmpty($setCookieHeader);
        $this->assertStringContainsString($sessionCookieName, $setCookieHeader);
        $this->assertStringContainsString('my-session-id', $setCookieHeader);
    }

    /**
     * Test if authentication is applied based on request attribute, when middleware is called as PSR-15 middleware.
     *
     * @dataProvider provideDataForPsr15Test
     * @param bool $applyOnExit
     */
    public function testUserIsAuthenticatedPsr15(
        bool $applyOnExit
    )
    {
        $user = new AuthenticatedUser(
            1,
            'ilija.studen@activecollab.com',
            'Ilija Studen',
            '123'
        );

        $userRepository = new UserRepository(
            [
                'ilija.studen@activecollab.com' => new AuthenticatedUser(
                    1,
                    'ilija.studen@activecollab.com',
                    'Ilija Studen',
                    '123'
                ),
            ]
        );

        $sessionRepository = new SessionRepository(
            [
                new Session(
                    'my-session-id',
                    'ilija.studen@activecollab.com'
                ),
            ]
        );

        $sessionCookieName = 'test-session-cookie';

        $sessionAdapter = new BrowserSessionAdapter(
            $userRepository,
            $sessionRepository,
            $this->cookies,
            $sessionCookieName
        );
        $session = $sessionAdapter->authenticate($user, []);

        /** @var ServerRequestInterface $request */
        $request = $this->request->withAttribute(
            'test_transport',
            new AuthorizationTransport(
                $sessionAdapter,
                $user,
                $session,
                [
                    1,
                    2,
                    3,
                ]
            ));

        $middleware = new ApplyAuthenticationMiddleware(
            new RequestValueContainer('test_transport'),
            $applyOnExit
        );

        $this->assertSame($applyOnExit, $middleware->applyOnExit());

        $response = $middleware->process(
            $request,
            new class implements RequestHandlerInterface
            {
                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    return (new ResponseFactory())->createResponse();
                }
            }
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $setCookieHeader = $response->getHeaderLine('Set-Cookie');

        $this->assertNotEmpty($setCookieHeader);
        $this->assertStringContainsString($sessionCookieName, $setCookieHeader);
        $this->assertStringContainsString('my-session-id', $setCookieHeader);
    }

    public function provideDataForPsr15Test(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * Test if next middleware in stack is called.
     */
    public function testNextIsCalled()
    {
        /** @var ResponseInterface $response */
        $response = call_user_func(
            new ApplyAuthenticationMiddleware(
                new RequestValueContainer('test_transport')
            ),
            $this->request,
            $this->response,
            function (
                ServerRequestInterface $request,
                ResponseInterface $response,
                callable $next = null
            ): ResponseInterface
            {
                $response = $response->withHeader('X-Test', 'Yes, found!');

                if ($next) {
                    $response = $next($request, $response);
                }

                return $response;
            }
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('Yes, found!', $response->getHeaderLine('X-Test'));
    }

    public function testProcessIsCalled()
    {
        $middlware = new ApplyAuthenticationMiddleware(
            new RequestValueContainer('test_transport')
        );

        $handler = new class implements RequestHandlerInterface
        {
            private $handleIsCalled = false;

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->handleIsCalled = true;
                return (new ResponseFactory())->createResponse();
            }

            public function isHandleCalled(): bool
            {
                return $this->handleIsCalled;
            }
        };

        $response = $middlware->process($this->request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertTrue($handler->isHandleCalled());
    }

    /**
     * Test if authentication is applied based on request attribute.
     */
    public function testUserIsAuthentiatedOnExit()
    {
        $user = new AuthenticatedUser(
            1,
            'ilija.studen@activecollab.com',
            'Ilija Studen',
            '123'
        );
        $user_repository = new UserRepository(
            [
                'ilija.studen@activecollab.com' => new AuthenticatedUser(
                    1,
                    'ilija.studen@activecollab.com',
                    'Ilija Studen',
                    '123'
                ),
            ]
        );
        $session_repository = new SessionRepository(
            [
                new Session('my-session-id', 'ilija.studen@activecollab.com')
            ]
        );

        $session_cookie_name = 'test-session-cookie';

        $session_adapter = new BrowserSessionAdapter(
            $user_repository,
            $session_repository,
            $this->cookies,
            $session_cookie_name
        );
        $session = $session_adapter->authenticate($user, []);

        /** @var ServerRequestInterface $request */
        $request = $this->request->withAttribute(
            'test_transport',
            new AuthorizationTransport(
                $session_adapter,
                $user,
                $session,
                [
                    1,
                    2,
                    3
                ]
            )
        );

        $middleware = new ApplyAuthenticationMiddleware(
            new RequestValueContainer('test_transport'),
            true
        );
        $this->assertTrue($middleware->applyOnExit());

        /** @var ResponseInterface $response */
        $response = call_user_func(
            $middleware,
            $request,
            $this->response,
            function (
                ServerRequestInterface $request,
                ResponseInterface $response,
                callable $next = null
            ): ResponseInterface
            {
                $this->assertEmpty($response->getHeaderLine('Set-Cookie'));

                if ($next) {
                    $response = $next($request, $response);
                }

                return $response;
            }
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $set_cookie_header = $response->getHeaderLine('Set-Cookie');

        $this->assertNotEmpty($set_cookie_header);
        $this->assertStringContainsString($session_cookie_name, $set_cookie_header);
        $this->assertStringContainsString('my-session-id', $set_cookie_header);
    }
}
