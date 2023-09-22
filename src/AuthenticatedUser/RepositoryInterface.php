<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\AuthenticatedUser;

interface RepositoryInterface
{
    /**
     * @param  int                             $user_id
     * @return AuthenticatedUserInterface|null
     */
    public function findById($user_id);

    /**
     * @param  string                          $username
     * @return AuthenticatedUserInterface|null
     */
    public function findByUsername($username);
}
