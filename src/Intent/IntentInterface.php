<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Intent;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;

interface IntentInterface
{
    public function isFulfilled(): bool;
    public function isExpired(): bool;
    public function belongsToUser(AuthenticatedUserInterface $user): bool;
    public function fulfill(
        AuthenticatedUserInterface $user,
        array $credentials,
    ): void;
}
