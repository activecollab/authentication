<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Authentication;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\BrowserSessionTestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class EventsTest extends BrowserSessionTestCase
{
    public function testOnUserAuthenticated()
    {
        $middleware = $this->prepareForAuthorization();

        $first_callback_called = false;
        $middleware->onUserAuthenticated(function ($arg_1) use (&$first_callback_called) {
            $this->validateOnUserAuthenticatedArguments(func_get_args());

            $first_callback_called = true;
        });

        $secon_callback_called = false;
        $middleware->onUserAuthenticated(function ($arg_1) use (&$secon_callback_called) {
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

    public function testOnUserSet()
    {
        $middleware = $this->prepareForAuthorization();

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
        $this->prepareForAuthorization()->setOnAuthenciatedUserChanged();
    }

    private function validateOnUserSetArguments(array $event_arguments)
    {
        $this->assertCount(1, $event_arguments);

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $event_arguments[0]);
    }

    private function prepareForAuthorization($session_id = 'my-session-id')
    {
        $this->setCookie('sessid', $session_id);

        new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123');
        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);
        $session_repository = new SessionRepository([new Session($session_id, 'ilija.studen@activecollab.com')]);

        return new Authentication([
            new BrowserSessionAdapter($user_repository, $session_repository, $this->cookies),
        ]);
    }
}
