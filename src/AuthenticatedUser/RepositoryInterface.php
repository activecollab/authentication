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
    public function findById(string|int $user_id): ?AuthenticatedUserInterface;
    public function findByUsername(string $username): ?AuthenticatedUserInterface;
}
