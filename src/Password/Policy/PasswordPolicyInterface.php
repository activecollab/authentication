<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Password\Policy;

use JsonSerializable;

interface PasswordPolicyInterface extends JsonSerializable
{
    /**
     * Return min password length. If this function returns 0, system will not check password length.
     */
    public function getMinLength(): int;

    /**
     * Returns true if system requires that passwords contain numbers.
     */
    public function requireNumbers(): bool;

    /**
     * Returns true if system requires that passwords contain numbers.
     */
    public function requireMixedCase(): bool;

    /**
     * Returns true if system requires that passwords contain symbols.
     */
    public function requireSymbols(): bool;
}
