<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Authentication;
use ActiveCollab\Authentication\AuthenticationInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authorization\AuthorizationTransportInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use ActiveCollab\Authentication\Authorizer\LocalAuthorizer;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\BrowserSessionTestCase;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Test
 */
class EventsTest extends BrowserSessionTestCase
{
    public function testOnUserAuthenticated()
    {
        $middleware = $this->prepareForAuthentication();

        $first_callback_called = false;
        $middleware->onUserAuthenticated(function () use (&$first_callback_called) {
            $this->validateOnUserAuthenticatedArguments(func_get_args());

            $first_callback_called = true;
        });

        $secon_callback_called = false;
        $middleware->onUserAuthenticated(function () use (&$secon_callback_called) {
            $this->validateOnUserAuthenticatedArguments(func_get_args());

            $secon_callback_called = true;
        });

        call_user_func($middleware, $this->request, $this->response);

        $this->assertTrue($first_callback_called);
        $this->assertTrue($secon_callback_called);
    }

    private function validateOnUserAuthenticatedArguments(array $event_arguments)
    {
        $this->assertCount(2, $event_arguments);

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $event_arguments[0]);
        $this->assertInstanceOf(AuthenticationResultInterface::class, $event_arguments[1]);
    }

    public function testOnUserDeauthenticated()
    {
        $authentication = $this->prepareForAuthentication();

        $first_callback_called = false;
        $authentication->onUserDeauthenticated(function () use (&$first_callback_called) {
            $this->validateOnUserDeauthenticatedArguments(func_get_args());

            $first_callback_called = true;
        });

        $secon_callback_called = false;
        $authentication->onUserDeauthenticated(function () use (&$secon_callback_called) {
            $this->validateOnUserDeauthenticatedArguments(func_get_args());

            $secon_callback_called = true;
        });

        /** @var ServerRequestInterface $modified_request */
        $modified_request = null;

        $response = call_user_func($authentication, $this->request, $this->response, function (ServerRequestInterface $request, ResponseInterface $response, callable $next = null) use (&$modified_request) {
            $modified_request = $request;

            if ($next) {
                $response = $next($request, $response);
            }

            return $response;
        });
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertFalse($first_callback_called);
        $this->assertFalse($secon_callback_called);

        $this->assertInstanceOf(ServerRequestInterface::class, $modified_request);

        $adapter = $modified_request->getAttribute('authentication_adapter');
        $authenticated_with = $modified_request->getAttribute('authenticated_with');

        $this->assertInstanceOf(AdapterInterface::class, $adapter);
        $this->assertInstanceOf(AuthenticationResultInterface::class, $authenticated_with);

        $authentication->terminate($adapter, $authenticated_with);

        $this->assertTrue($first_callback_called);
        $this->assertTrue($secon_callback_called);
    }

    private function validateOnUserDeauthenticatedArguments(array $event_arguments)
    {
        $this->assertCount(1, $event_arguments);

        $this->assertInstanceOf(AuthenticationResultInterface::class, $event_arguments[0]);
    }

    public function testOnUserSet()
    {
        $middleware = $this->prepareForAuthentication();

        $first_callback_called = false;
        $middleware->onUserSet(function () use (&$first_callback_called) {
            $this->validateOnUserSetArguments(func_get_args());

            $first_callback_called = true;
        });

        $deprecated_callback_called = false;
        $middleware->setOnAuthenciatedUserChanged(function () use (&$deprecated_callback_called) {
            $this->validateOnUserSetArguments(func_get_args());

            $deprecated_callback_called = true;
        });

        call_user_func($middleware, $this->request, $this->response);

        $this->assertTrue($first_callback_called);
        $this->assertTrue($deprecated_callback_called);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value needs to be a callable.
     */
    public function testInvalidUserChangedMethodCall()
    {
        $this->prepareForAuthentication()->setOnAuthenciatedUserChanged();
    }

    private function validateOnUserSetArguments(array $event_arguments)
    {
        $this->assertCount(1, $event_arguments);

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $event_arguments[0]);
    }

    public function testOnUserAuthorized()
    {
        /** @var AuthenticationInterface $authentication */
        /** @var AdapterInterface $adapter */
        /** @var AuthorizerInterface $authorizer */
        list ($authentication, $adapter, $authorizer) = $this->prepareForAuthorization();

        $first_callback_called = false;
        $authentication->onUserAuthorized(function () use (&$first_callback_called) {
            $this->validateOnUserAuthorizedArguments(func_get_args());

            $first_callback_called = true;
        });

        $secon_callback_called = false;
        $authentication->onUserAuthorized(function () use (&$secon_callback_called) {
            $this->validateOnUserAuthorizedArguments(func_get_args());

            $secon_callback_called = true;
        });

        $authorization = $authentication->authorize($authorizer, $adapter, [
            'username' => 'ilija.studen@activecollab.com',
            'password' => '123',
        ]);
        $this->assertInstanceOf(AuthorizationTransportInterface::class, $authorization);

        $this->assertTrue($first_callback_called);
        $this->assertTrue($secon_callback_called);
    }

    private function validateOnUserAuthorizedArguments(array $event_arguments)
    {
        $this->assertCount(2, $event_arguments);

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $event_arguments[0]);
        $this->assertInstanceOf(AuthenticationResultInterface::class, $event_arguments[1]);
    }

    public function testOnUserAuthorizationFailed()
    {
        /** @var AuthenticationInterface $authentication */
        /** @var AdapterInterface $adapter */
        /** @var AuthorizerInterface $authorizer */
        list ($authentication, $adapter, $authorizer) = $this->prepareForAuthorization();

        $first_callback_called = false;
        $authentication->onUserAuthorizationFailed(function () use (&$first_callback_called) {
            $this->validateOnUserAuthorizationFailedArguments(func_get_args());

            $first_callback_called = true;
        });

        $secon_callback_called = false;
        $authentication->onUserAuthorizationFailed(function () use (&$secon_callback_called) {
            $this->validateOnUserAuthorizationFailedArguments(func_get_args());

            $secon_callback_called = true;
        });

        try {
            $authentication->authorize($authorizer, $adapter, [
                'username' => 'ilija.studen@activecollab.com',
                'password' => 'invalid password',
            ]);
        } catch (Exception $e) {
        }

        $this->assertTrue($first_callback_called);
        $this->assertTrue($secon_callback_called);
    }

    private function validateOnUserAuthorizationFailedArguments(array $event_arguments)
    {
        $this->assertCount(2, $event_arguments);

        $this->assertInternalType('array', $event_arguments[0]);
        $this->assertInstanceOf(Exception::class, $event_arguments[1]);
    }

    private function prepareForAuthentication($session_id = 'my-session-id')
    {
        $this->setCookie('sessid', $session_id);

        new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123');
        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);
        $session_repository = new SessionRepository([new Session($session_id, 'ilija.studen@activecollab.com')]);

        return new Authentication(
            new BrowserSessionAdapter($user_repository, $session_repository, $this->cookies)
        );
    }

    private function prepareForAuthorization($session_id = 'my-session-id')
    {
        new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123');
        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);
        $session_repository = new SessionRepository([new Session($session_id, 'ilija.studen@activecollab.com')]);

        $browser_session_adapter = new BrowserSessionAdapter($user_repository, $session_repository, $this->cookies);
        $authentication = new Authentication($browser_session_adapter);

        return [$authentication, $browser_session_adapter, new LocalAuthorizer($user_repository)];
    }
}
