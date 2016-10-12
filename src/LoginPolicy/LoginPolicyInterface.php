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

    const VALID_USERNAME_FORMATS = [self::USERNAME_FORMAT_TEXT, self::USERNAME_FORMAT_EMAIL];

    /**
     * Return username format.
     *
     * @return string
     */
    public function getUsernameFormat();

    /**
     * Set username format.
     *
     * @param  string $value
     * @return $this
     */
    public function &setUsernameFormat($value);

    /**
     * Enable Remember Me feature to support extended sessions.
     *
     * @return bool
     */
    public function rememberExtendsSession();

    /**
     * Set value of Remember Me flag.
     *
     * @param  bool  $value
     * @return $this
     */
    public function &setRememberExtendsSession($value);

    /**
     * Password change is supported by this authentication adapter.
     *
     * @return bool
     */
    public function isPasswordChangeEnabled();

    /**
     * Set value of is password enabled flag.
     *
     * @param  bool  $value
     * @return $this
     */
    public function &setIsPasswordChangeEnabled($value);

    /**
     * Return true if this authentication adapter supports password recovery.
     *
     * @return bool
     */
    public function isPasswordRecoveryEnabled();

    /**
     * Set value of is password recovery enabled flag.
     *
     * @param  bool  $value
     * @return $this
     */
    public function &setIsPasswordRecoveryEnabled($value);

    /**
     * Get login URL.
     *
     * @return string|null
     */
    public function getExternalLoginUrl();

    /**
     * Set external login URL.
     *
     * @param  string $value
     * @return $this
     */
    public function &setExternalLoginUrl($value);

    /**
     * Get logout URL.
     *
     * @return string
     */
    public function getExternalLogoutUrl();

    /**
     * Set external log out URL.
     *
     * @param  string $value
     * @return $this
     */
    public function &setExternalLogoutUrl($value);

    /**
     * Get change password URL.
     *
     * @return string|null
     */
    public function getExternalChangePasswordUrl();

    /**
     * Set external change password URL.
     *
     * @param  string $value
     * @return $this
     */
    public function &setExternalChangePasswordUrl($value);

    /**
     * Get update profile URL.
     *
     * @return string|null
     */
    public function getExternalUpdateProfileUrl();

    /**
     * Set external update profile URL.
     *
     * @param  string $value
     * @return $this
     */
    public function &setExternalUpdateProfileUrl($value);
}
