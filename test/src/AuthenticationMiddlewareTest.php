<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\Adapter\BrowserSessionAdapterInterface;
use ActiveCollab\Authentication\Adapter\TokenBearerAdapter;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Authentication;
use ActiveCollab\Authentication\AuthenticationInterface;
use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;
use ActiveCollab\Authentication\Intent\RepositoryInterface as IntentRepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\RequestResponseTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;
use ActiveCollab\Authentication\Token\TokenInterface;
use ActiveCollab\Cookies\Cookies;
use ActiveCollab\Cookies\CookiesInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\ResponseFactory;

class AuthenticationMiddlewareTest extends RequestResponseTestCase
{
    private CookiesInterface $cookies;
    private string $browserSessionCookieName = 'test-session-cookie';
    private BrowserSessionAdapter $browserSessionAdapter;
    private TokenBearerAdapter $token_bearer_adapter;

    public function setUp(): void
    {
        parent::setUp();

        $this->cookies = new Cookies();

        $user_repository = new UserRepository(
            [
                'ilija.studen@activecollab.com' => new AuthenticatedUser(
                    1, 'ilija.studen@activecollab.com',
                    'Ilija Studen',
                    '123'
                ),
            ]
        );

        $session_repository = new SessionRepository(
            [
                new Session(
                    'my-session-id',
                    'ilija.studen@activecollab.com'
                )
            ]
        );
        $this->browserSessionAdapter = new BrowserSessionAdapter(
            $user_repository,
            $session_repository,
            $this->cookies,
            $this->browserSessionCookieName
        );

        $token_repository = new TokenRepository(
            [
                'awesome-token' => new Token(
                    'awesome-token',
                    'ilija.studen@activecollab.com'
                )
            ]
        );
        $this->token_bearer_adapter = new TokenBearerAdapter(
            $user_repository,
            $token_repository
        );
    }

    public function testMiddlewareAcceptsMultipleAdapters(): void
    {
        $middleware = new Authentication(
            $this->createMock(IntentRepositoryInterface::class),
            $this->browserSessionAdapter,
            $this->token_bearer_adapter
        );

        $this->assertIsArray($middleware->getAdapters());
        $this->assertCount(2, $middleware->getAdapters());
    }

    /**
     * Test that user is authenticated.
     */
    public function testBrowserSessionAuthentication(): void
    {
        /** @var ServerRequestInterface $request */
        /** @var ResponseInterface $response */
        [
            $request,
            $response,
        ] = $this->cookies->set($this->request, $this->response, $this->browserSessionCookieName, 'my-session-id');

        $middleware = new Authentication(
            $this->createMock(IntentRepositoryInterface::class),
            $this->browserSessionAdapter
        );

        /** @var ServerRequestInterface $modifiedRequest */
        $modifiedRequest = null;

        /** @var ResponseInterface $response */
        $response = call_user_func(
            $middleware,
            $request,
            $response,
            function (ServerRequestInterface $request, ResponseInterface $response, callable $next = null) use (&$modifiedRequest) {
                $modifiedRequest = $request;

                if ($next) {
                    $response = $next($request, $response);
                }

                return $response;
            }
        );

        $this->assertInstanceOf(ServerRequestInterface::class, $modifiedRequest);
        $this->assertInstanceOf(ResponseInterface::class, $response);

        // Test if authentication attributes are set
        $this->assertArrayHasKey('authentication_adapter', $modifiedRequest->getAttributes());
        $this->assertArrayHasKey('authenticated_user', $modifiedRequest->getAttributes());
        $this->assertArrayHasKey('authenticated_with', $modifiedRequest->getAttributes());

        // Test if session cookie is set
        $setCookieHeader = $response->getHeaderLine('Set-Cookie');

        $this->assertNotEmpty($setCookieHeader);
        $this->assertStringContainsString($this->browserSessionCookieName, $setCookieHeader);
        $this->assertStringContainsString('my-session-id', $setCookieHeader);

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $middleware->getAuthenticatedUser());
        $this->assertInstanceOf(SessionInterface::class, $middleware->getAuthenticatedWith());
    }

    /**
     * Test that user is authenticated when middleware is invoked as PSR-15 middleware.
     */
    public function testBrowserSessionAuthenticationPsr15(): void
    {
        /** @var ServerRequestInterface $request */
        $request = $this->cookies
            ->createSetter($this->browserSessionCookieName, 'my-session-id')
                ->applyToRequest($this->request);

        $this->assertInstanceOf(ServerRequestInterface::class, $request);

        $middleware = new Authentication(
            $this->createMock(IntentRepositoryInterface::class),
            $this->browserSessionAdapter,
        );

        $requestHandler = new class implements RequestHandlerInterface
        {
            private ServerRequestInterface $capturedRequest;

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->capturedRequest = $request;

                return (new ResponseFactory())->createResponse();
            }

            public function getCapturedRequest(): ServerRequestInterface
            {
                return $this->capturedRequest;
            }
        };

        $response = $middleware->process($request, $requestHandler);

        $modifiedRequest = $requestHandler->getCapturedRequest();

        $this->assertInstanceOf(ServerRequestInterface::class, $modifiedRequest);
        $this->assertInstanceOf(ResponseInterface::class, $response);

        // Test if authentication attributes are set
        $this->assertArrayHasKey('authentication_adapter', $modifiedRequest->getAttributes());
        $this->assertArrayHasKey('authenticated_user', $modifiedRequest->getAttributes());
        $this->assertArrayHasKey('authenticated_with', $modifiedRequest->getAttributes());

        // Test if session cookie is set
        $setCookieHeader = $response->getHeaderLine('Set-Cookie');

        $this->assertNotEmpty($setCookieHeader);
        $this->assertStringContainsString($this->browserSessionCookieName, $setCookieHeader);
        $this->assertStringContainsString('my-session-id', $setCookieHeader);

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $middleware->getAuthenticatedUser());
        $this->assertInstanceOf(SessionInterface::class, $middleware->getAuthenticatedWith());
    }

    /**
     * Test that user is authenticated when middleware is invoked as PSR-15 middleware.
     */
    public function testBrowserSessionAuthenticationPsr15WithLogout(): void
    {
        /** @var ServerRequestInterface $request */
        $request = $this->cookies
            ->createSetter($this->browserSessionCookieName, 'my-session-id')
            ->applyToRequest($this->request);

        $this->assertInstanceOf(ServerRequestInterface::class, $request);

        $middleware = new Authentication(
            $this->createMock(IntentRepositoryInterface::class),
            $this->browserSessionAdapter,
        );

        $requestHandler = new class ($middleware, $this->browserSessionAdapter) implements RequestHandlerInterface
        {
            private ?ServerRequestInterface $capturedRequest = null;

            public function __construct(
                private AuthenticationInterface $authentication,
                private BrowserSessionAdapterInterface $browserSessionAdapter
            )
            {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->capturedRequest = $request;

                $this->authentication->terminate(
                    $this->browserSessionAdapter,
                    $request->getAttribute('authenticated_with')
                );

                return (new ResponseFactory())->createResponse();
            }

            public function getCapturedRequest(): ServerRequestInterface
            {
                return $this->capturedRequest;
            }
        };

        $response = $middleware->process($request, $requestHandler);

        $modifiedRequest = $requestHandler->getCapturedRequest();

        $this->assertInstanceOf(ServerRequestInterface::class, $modifiedRequest);
        $this->assertInstanceOf(ResponseInterface::class, $response);

        // Test if authentication attributes are set
        $this->assertArrayHasKey('authentication_adapter', $modifiedRequest->getAttributes());
        $this->assertArrayHasKey('authenticated_user', $modifiedRequest->getAttributes());
        $this->assertArrayHasKey('authenticated_with', $modifiedRequest->getAttributes());

        // Test if session cookie is set
        $setCookieHeader = $response->getHeaderLine('Set-Cookie');

        $this->assertNotEmpty($setCookieHeader);
        $this->assertStringContainsString($this->browserSessionCookieName, $setCookieHeader);

        $cookieValue = \Dflydev\FigCookies\Cookies::fromCookieString($setCookieHeader)
            ->get('test-session-cookie')
                ->getValue();

        $this->assertSame('', $cookieValue);

        $expirationString = \Dflydev\FigCookies\Cookies::fromCookieString($setCookieHeader)
            ->get('Expires')
                ->getValue();

        $expirationTimestamp = strtotime($expirationString);

        $this->assertNotFalse($expirationTimestamp);
        $this->assertLessThan(time() - 1, $expirationTimestamp);

        $this->assertNull($middleware->getAuthenticatedUser());
        $this->assertNull($middleware->getAuthenticatedWith());
    }

    public function testTokenBearerAuthentication(): void
    {
        $request = $this->request->withHeader('Authorization', 'Bearer awesome-token');

        $middleware = new Authentication(
            $this->createMock(IntentRepositoryInterface::class),
            $this->token_bearer_adapter,
        );

        /** @var ServerRequestInterface $modifiedRequest */
        $modifiedRequest = null;

        $response = call_user_func(
            $middleware,
            $request,
            $this->response,
            function (
                ServerRequestInterface $request,
                ResponseInterface $response,
                callable $next = null
            ) use (&$modifiedRequest) {
                $modifiedRequest = $request;

                if ($next) {
                    $response = $next($request, $response);
                }

                return $response;
            }
        );

        $this->assertInstanceOf(ServerRequestInterface::class, $modifiedRequest);
        $this->assertInstanceOf(ResponseInterface::class, $response);

        // Test if authentication attributes are set
        $this->assertArrayHasKey('authentication_adapter', $modifiedRequest->getAttributes());
        $this->assertArrayHasKey('authenticated_user', $modifiedRequest->getAttributes());
        $this->assertArrayHasKey('authenticated_with', $modifiedRequest->getAttributes());

        // Test if session cookie is set
        $setCookieHeader = $response->getHeaderLine('Set-Cookie');
        $this->assertEmpty($setCookieHeader);

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $middleware->getAuthenticatedUser());
        $this->assertInstanceOf(TokenInterface::class, $middleware->getAuthenticatedWith());
    }

    public function testTokenBearerAuthenticationPsr15(): void
    {
        $request = $this->request->withHeader('Authorization', 'Bearer awesome-token');

        $middleware = new Authentication(
            $this->createMock(IntentRepositoryInterface::class),
            $this->token_bearer_adapter,
        );

        $requestHandler = new class implements RequestHandlerInterface
        {
            private ServerRequestInterface $capturedRequest;

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->capturedRequest = $request;

                return (new ResponseFactory())->createResponse();
            }

            public function getCapturedRequest(): ServerRequestInterface
            {
                return $this->capturedRequest;
            }
        };

        $response = $middleware->process($request, $requestHandler);

        $modifiedRequest = $requestHandler->getCapturedRequest();

        $this->assertInstanceOf(ServerRequestInterface::class, $modifiedRequest);
        $this->assertInstanceOf(ResponseInterface::class, $response);

        // Test if authentication attributes are set
        $this->assertArrayHasKey('authentication_adapter', $modifiedRequest->getAttributes());
        $this->assertArrayHasKey('authenticated_user', $modifiedRequest->getAttributes());
        $this->assertArrayHasKey('authenticated_with', $modifiedRequest->getAttributes());

        // Test if session cookie is set
        $setCookieHeader = $response->getHeaderLine('Set-Cookie');
        $this->assertEmpty($setCookieHeader);

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $middleware->getAuthenticatedUser());
        $this->assertInstanceOf(TokenInterface::class, $middleware->getAuthenticatedWith());
    }

    public function testExceptionOnMultipleIds(): void
    {
        $this->expectException(InvalidAuthenticationRequestException::class);
        $this->expectExceptionMessage('You can not be authenticated with more than one authentication method');

        /** @var ServerRequestInterface $request */
        /** @var ResponseInterface $response */
        [
            $request,
            $response,
        ] = $this->cookies->set(
            $this->request,
            $this->response,
            $this->browserSessionCookieName,
            'my-session-id'
        );

        /** @var ServerRequestInterface $request */
        $request = $request->withHeader('Authorization', 'Bearer awesome-token');

        call_user_func(
            new Authentication(
                $this->createMock(IntentRepositoryInterface::class),
                $this->browserSessionAdapter,
                $this->token_bearer_adapter,
            ),
            $request,
            $response
        );
    }
}
