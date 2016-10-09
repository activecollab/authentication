<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Password\Policy;

/**
 * @package ActiveCollab\Authentication\Password
 */
interface PasswordPolicyInterface extends \JsonSerializable
{
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
