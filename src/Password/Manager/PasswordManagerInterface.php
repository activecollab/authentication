<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Password\Manager;

/**
 * @package ActiveCollab\Authentication\Password
 */
interface PasswordManagerInterface
{
    const HASHED_WITH_SHA1 = 'sha1';
    const HASHED_WITH_PBKDF2 = 'pbkdf2';
    const HASHED_WITH_PHP = 'php';

    /**
     * Verify if $password matches the value that we have hashed.
     *
     * @param  string $password
     * @param  string $hash
     * @param  string $hashed_with
     * @return bool
     */
    public function verify($password, $hash, $hashed_with);

    /**
     * Hash the password using given hashing mechanism.
     *
     * @param  string $password
     * @param  string $hash_with
     * @return string
     */
    public function hash($password, $hash_with = self::HASHED_WITH_PHP);

    /**
     * Check if password needs rehashing.
     *
     * @param  string $hash
     * @param  string $hashed_with
     * @return bool
     */
    public function needsRehash($hash, $hashed_with);
}
