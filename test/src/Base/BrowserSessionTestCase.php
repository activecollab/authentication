<?php

namespace ActiveCollab\Authentication\Test\Base;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Session\RepositoryInterface as SessionRepositoryInterface;
use ActiveCollab\Authentication\Test\Session\Repository as SessionRepository;

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
     * Set up test environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->empty_users_repository = new UserRepository();
        $this->empty_sessions_repository = new SessionRepository();
    }
}
