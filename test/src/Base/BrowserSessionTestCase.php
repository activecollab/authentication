<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Base;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\Session\RepositoryInterface as SessionRepositoryInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;
use ActiveCollab\Cookies\Adapter\Adapter;
use ActiveCollab\Cookies\Cookies;
use ActiveCollab\Cookies\CookiesInterface;

/**
 * @package ActiveCollab\Authentication\Test
 */
abstract class BrowserSessionTestCase extends RequestResponseTestCase
{
    /**
     * @var UserRepositoryInterface
     */
    protected $empty_users_repository;

    /**
     * @var SessionRepositoryInterface
     */
    protected $empty_sessions_repository;

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

        $this->empty_users_repository = new UserRepository();
        $this->empty_sessions_repository = new SessionRepository();
        $this->cookies = new Cookies(new Adapter());
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
