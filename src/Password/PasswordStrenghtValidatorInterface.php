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
interface PasswordStrenghtValidatorInterface
{
    /**
     * Return true if password meets the criteria set by the password policy.
     *
     * @param  string                  $password
     * @param  PasswordPolicyInterface $policy
     * @return bool
     */
    public function isPasswordValid($password, PasswordPolicyInterface $policy);
}
