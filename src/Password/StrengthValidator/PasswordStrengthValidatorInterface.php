<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Password\StrengthValidator;

use ActiveCollab\Authentication\Password\Policy\PasswordPolicyInterface;

interface PasswordStrengthValidatorInterface
{
    const TOO_SHORT = 1;
    const NO_NUMBERS = 2;
    const NO_MIXED_CASE = 4;
    const NO_SYMBOLS = 8;

    /**
     * Return true if password meets the criteria set by the password policy.
     */
    public function validate(
        string $password,
        PasswordPolicyInterface $policy,
    ): bool;

    /**
     * Generate a new password of the required strength and length.
     */
    public function generateValid(
        int $length,
        PasswordPolicyInterface $policy,
    ): string;
}
