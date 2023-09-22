<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Password\Manager;

interface PasswordManagerInterface
{
    public function verify(string $password, string $hash): bool;
    public function hash(string $password): string;
    public function needsRehash(string $hash): bool;
}
