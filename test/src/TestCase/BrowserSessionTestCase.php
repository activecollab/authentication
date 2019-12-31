<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\TestCase;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\Session\RepositoryInterface as SessionRepositoryInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Cookies\Cookies;
use ActiveCollab\Cookies\CookiesInterface;

abstract class BrowserSessionTestCase extends RequestResponseTestCase
{
    /**
     * @var UserRepositoryInterface
     */
    protected $empty_user_repository;

    /**
     * @var SessionRepositoryInterface
     */
    protected $empty_session_repository;

    /**
     * @var CookiesInterface
     */
    protected $cookies;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->empty_user_repository = new UserRepository();
        $this->empty_session_repository = new SessionRepository();
        $this->cookies = new Cookies();
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setCookie($name, $value)
    {
        list($this->request, $this->response) = $this->cookies->set($this->request, $this->response, $name, $value);
    }
}
