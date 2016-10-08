<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Password\StrengthValidator;

use ActiveCollab\Authentication\Password\Policy\PasswordPolicyInterface;

/**
 * @package ActiveCollab\Authentication\Password
 */
interface PasswordStrengthValidatorInterface
{
    const TOO_SHORT = 1;
    const NO_NUMBERS = 2;
    const NO_MIXED_CASE = 4;
    const NO_SYMBOLS = 8;

    /**
     * Return true if password meets the criteria set by the password policy.
     *
     * @param  string                  $password
     * @param  PasswordPolicyInterface $policy
     * @return bool
     */
    public function validate($password, PasswordPolicyInterface $policy);

    /**
     * Generate a new password of the required strength and length.
     *
     * @param  int                     $length
     * @param  PasswordPolicyInterface $policy
     * @return string
     */
    public function generateValid($length, PasswordPolicyInterface $policy);
}
