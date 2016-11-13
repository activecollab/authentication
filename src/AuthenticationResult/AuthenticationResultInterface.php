<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\AuthenticationResult;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use JsonSerializable;

/**
 * @package ActiveCollab\Authentication\AuthenticationResult
 */
interface AuthenticationResultInterface extends JsonSerializable
{
    /**
     * Get authenticated user from the repository.
     *
     * @param  RepositoryInterface        $repository
     * @return AuthenticatedUserInterface
     */
    public function getAuthenticatedUser(RepositoryInterface $repository);
}
