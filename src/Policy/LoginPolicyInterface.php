<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Policy;

use JsonSerializable;

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
     * Check is text username format.
     *
     * @return bool
     */
    public function isUsernameTextFormat();

    /**
     * Check is email username format.
     *
     * @return bool
     */
    public function isUsernameEmailFormat();

    /**
     * Enable Remember Me feature to support extended sessions.
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
    public function getLoginUrl();

    /**
     * Get logout URL.
     *
     * @return string
     */
    public function getLogoutUrl();

    /**
     * Get change password URL.
     *
     * @return string|null
     */
    public function getChangePasswordUrl();

    /**
     * Get update profile URL.
     *
     * @return string|null
     */
    public function getUpdateProfileUrl();
}
