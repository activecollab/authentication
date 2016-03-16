<?php

/*
 * This file is part of the Active Collab ID project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Base;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Token\RepositoryInterface as TokenRepositoryInterface;

/**
 * @package ActiveCollab\Authentication\Test
 */
abstract class TokenBearerTestCase extends RequestResponseTestCase
{
    /**
     * @var UserRepositoryInterface
     */
    protected $empty_users_repository;

    /**
     * @var TokenRepositoryInterface
     */
    protected $empty_tokens_repository;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->empty_users_repository = new UserRepository();
        $this->empty_tokens_repository = new TokenRepository();
    }
}
