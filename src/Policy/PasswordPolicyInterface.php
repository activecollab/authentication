<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Policy;

use JsonSerializable;

interface PasswordPolicyInterface extends JsonSerializable
{
    // Password hashing mechanism
    const HASHED_WITH_SHA1 = 'sha1';
    const HASHED_WITH_PBKDF2 = 'pbkdf2';

    // Password checker responses
    const PASSWORD_TOO_SHORT = 1;
    const PASSWORD_HAS_NO_NUMBERS = 2;
    const PASSWORD_HAS_NO_SYMBOLS = 3;

    /**
     * @param  string $value
     * @return bool
     */
    public function isHashedWithSha1($value);

    /**
     * @param  string $value
     * @return bool
     */
    public function isHashedWithPbkdf2($value);

    /**
     * @param  string $value
     * @return bool
     */
    public function isToShort($value);

    /**
     * @param  string $value
     * @return bool
     */
    public function isWithoutNumbers($value);

    /**
     * @param  string $value
     * @return bool
     */
    public function isWithoutSymbols($value);
}
