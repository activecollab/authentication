<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Intent;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;

interface RepositoryInterface
{
    public function createIntent(
        AuthenticatedUserInterface $user,
        array $arguments = null,
    ): IntentInterface;
}
