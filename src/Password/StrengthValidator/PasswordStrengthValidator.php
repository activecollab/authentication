<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Password\StrengthValidator;

use ActiveCollab\Authentication\Exception\InvalidPasswordException;
use ActiveCollab\Authentication\Password\Policy\PasswordPolicyInterface;
use LogicException;
use RuntimeException;

class PasswordStrengthValidator implements PasswordStrengthValidatorInterface
{
    public function validate(
        string $password,
        PasswordPolicyInterface $policy
    ): bool
    {
        $password = trim($password);

        if (empty($password)) {
            throw new InvalidPasswordException('Password is empty');
        }

        $errors = 0;

        if ($policy->getMinLength() && mb_strlen($password) < $policy->getMinLength()) {
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

    public function generateValid(
        int $length,
        PasswordPolicyInterface $policy,
    ): string
    {
        if ($length < 16) {
            throw new LogicException('Minimal password length that this utility can generate is 16 characters');
        }

        if ($length < $policy->getMinLength()) {
            throw new LogicException('Password policy requires longer password');
        }

        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        if ($policy->requireSymbols()) {
            $characters .= ',.;:!$%^&';
        }

        $counter = 0;

        while (++$counter < 10000) {
            try {
                $password = $this->generateRandomString($length, $characters);

                if ($this->validate($password, $policy)) {
                    return $password;
                }
            } catch (InvalidPasswordException) {
            }
        }

        throw new RuntimeException('Failed to generate new password in 1000 iterations');
    }

    private function generateRandomString(int $length, string $characters): string
    {
        $result = '';

        $max = mb_strlen($characters, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $result .= $characters[random_int(0, $max)];
        }

        return $result;
    }
}
