<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\LoginPolicy;

use JsonSerializable;

/**
 * @package ActiveCollab\Authentication\LoginPolicy
 */
interface LoginPolicyInterface extends JsonSerializable
{
    const USERNAME_FORMAT_TEXT = 'text';
    const USERNAME_FORMAT_EMAIL = 'email';

    /**
     * Return username format.
     *
     * @return string
     */
    public function getUsernameFormat();

    /**
     * Enable Remember Me feature to support extended sessions.
     *
     * @return bool
     */
    public function rememberExtendsSession();

    /**
     * Password change is supported by this authentication adapter.
     *
     * @return bool
     */
    public function isPasswordChangeEnabled();

    /**
     * Return true if this authentication adapter supports password recovery.
     *
     * @return bool
     */
    public function isPasswordRecoveryEnabled();

    /**
     * Get login URL.
     *
     * @return string|null
     */
    public function getExternalLoginUrl();

    /**
     * Get logout URL.
     *
     * @return string
     */
    public function getExternalLogoutUrl();

    /**
     * Get change password URL.
     *
     * @return string|null
     */
    public function getExternalChangePasswordUrl();

    /**
     * Get update profile URL.
     *
     * @return string|null
     */
    public function getExternalUpdateProfileUrl();
}
