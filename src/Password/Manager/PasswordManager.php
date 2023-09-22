<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Password\Manager;

class PasswordManager implements PasswordManagerInterface
{
    public function __construct(
        private string $global_salt = '',
    )
    {
    }

    public function verify(string $password, string $hash): bool
    {
            return password_verify($this->global_salt . $password, $hash);
    }

    public function hash(string $password): string
    {
        return password_hash($this->global_salt . $password, PASSWORD_DEFAULT);
    }

    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }
}
