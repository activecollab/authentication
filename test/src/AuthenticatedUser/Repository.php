<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\AuthenticatedUser;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use LogicException;

class Repository implements RepositoryInterface
{
    private array $users_by_username = [];

    public function __construct(array $users_by_username = [])
    {
        foreach ($users_by_username as $user) {
            if (!$user instanceof AuthenticatedUserInterface) {
                throw new LogicException('Users by username can only include users');
            }

            $this->users_by_username[$user->getUsername()] = $user;
        }
    }

    public function findById(string|int $user_id): ?AuthenticatedUserInterface
    {
        throw new LogicException('This implementation does not support user ID');
    }

    public function findByUsername(string $username): ?AuthenticatedUserInterface
    {
        return $this->users_by_username[$username] ?? null;
    }
}
