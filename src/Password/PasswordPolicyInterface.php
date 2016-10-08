<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Password;

/**
 * @package ActiveCollab\Authentication\Password
 */
interface PasswordPolicyInterface
{
    // Password hashing mechanism
    const HASHED_WITH_SHA1 = 'sha1';
    const HASHED_WITH_PBKDF2 = 'pbkdf2';

    // Password checker responses
    const PASSWORD_TOO_SHORT = 1;
    const PASSWORD_HAS_NO_NUMBERS = 2;
    const PASSWORD_HAS_NO_SYMBOLS = 3;

    /**
     * Return min password length. If this function returns 0, system will not check password length.
     *
     * @return int
     */
    public function getMinLength();

    /**
     * Returns true if system requires that passwords contain numbers.
     *
     * @return bool
     */
    public function requireNumbers();

    /**
     * Returns true if system requires that passwords contain numbers.
     *
     * @return bool
     */
    public function requireMixedCase();

    /**
     * Returns true if system requires that passwords contain symbols.
     *
     * @return bool
     */
    public function requireSymbols();
}
