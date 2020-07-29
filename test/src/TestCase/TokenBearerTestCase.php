<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\TestCase;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Token\RepositoryInterface as TokenRepositoryInterface;

abstract class TokenBearerTestCase extends RequestResponseTestCase
{
    /**
     * @var UserRepositoryInterface
     */
    protected $empty_user_repository;

    /**
     * @var TokenRepositoryInterface
     */
    protected $empty_token_repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->empty_user_repository = new UserRepository();
        $this->empty_token_repository = new TokenRepository();
    }
}
