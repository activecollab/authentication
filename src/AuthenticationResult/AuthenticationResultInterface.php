<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\AuthenticationResult;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use JsonSerializable;

interface AuthenticationResultInterface extends JsonSerializable
{
    /**
     * Get authenticated user from the repository.
     */
    public function getAuthenticatedUser(RepositoryInterface $repository): AuthenticatedUserInterface;
}
