<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Password;

use ActiveCollab\Authentication\Exception\InvalidPasswordException;

/**
 * @package ActiveCollab\Authentication\Password
 */
class PasswordStrengthValidator implements PasswordStrengthValidatorInterface
{
    /**
     * Return true if password meets the criteria set by the password policy.
     *
     * @param  string                  $password
     * @param  PasswordPolicyInterface $policy
     * @return bool
     */
    public function isPasswordValid($password, PasswordPolicyInterface $policy)
    {
        if (!is_string($password)) {
            throw new InvalidPasswordException('Password is not a string');
        }

        $password = trim($password);

        if (empty($password)) {
            throw new InvalidPasswordException('Password is empty');
        }

        $errors = 0;

        if ($policy->getMinLength() && strlen($password) < $policy->getMinLength()) {
            $errors += PasswordStrengthValidatorInterface::TOO_SHORT;
        }

        if ($policy->requireNumbers() && !preg_match('#\d#', $password)) {
            $errors += PasswordStrengthValidatorInterface::NO_NUMBERS;
        }

        if ($policy->requireMixedCase() && !(preg_match('/[A-Z]+/', $password) && preg_match('/[a-z]+/', $password))) {
            $errors += PasswordStrengthValidatorInterface::NO_MIXED_CASE;
        }

        if ($policy->requireSymbols() && !preg_match('/[,.;:!$\\\\%^&~@#*\/]+/', $password)) {
            $errors += PasswordStrengthValidatorInterface::NO_SYMBOLS;
        }

        if ($errors > 0) {
            throw new InvalidPasswordException('Password is not strong enough', $errors);
        }

        return true;
    }
}
