<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\LoginPolicy;

use InvalidArgumentException;

/**
 * @package ActiveCollab\Authentication\LoginPolicy
 */
class LoginPolicy implements LoginPolicyInterface
{
    /**
     * @var string
     */
    private $username_format;

    /**
     * @var bool
     */
    private $remember_extends_session;

    /**
     * @var bool
     */
    private $password_change_enabled;

    /**
     * @var bool
     */
    private $password_recovery_enabled;

    /**
     * @var string
     */
    private $external_login_url;

    /**
     * @var string
     */
    private $external_logout_url;

    /**
     * @var string
     */
    private $external_change_password_url;

    /**
     * @var string
     */
    private $external_update_profile_url;

    /**
     * LoginPolicy constructor.
     *
     * @param string      $username_format
     * @param bool        $remember_extends_session
     * @param bool        $password_change_enabled
     * @param bool        $password_recovery_enabled
     * @param string|null $external_login_url
     * @param string|null $external_logout_url
     * @param string|null $external_change_password_url
     * @param string|null $external_update_profile_url
     */
    public function __construct($username_format = self::USERNAME_FORMAT_TEXT, $remember_extends_session = true, $password_change_enabled = true, $password_recovery_enabled = true, $external_login_url = null, $external_logout_url = null, $external_change_password_url = null, $external_update_profile_url = null)
    {
        $this->setUsernameFormat($username_format);

        $this->setRememberExtendsSession($remember_extends_session);
        $this->setIsPasswordChangeEnabled($password_change_enabled);
        $this->setIsPasswordRecoveryEnabled($password_recovery_enabled);

        $this->setExternalLoginUrl($external_login_url);
        $this->setExternalLogoutUrl($external_logout_url);
        $this->setExternalChangePasswordUrl($external_change_password_url);
        $this->setExternalUpdateProfileUrl($external_update_profile_url);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsernameFormat()
    {
        return $this->username_format;
    }

    /**
     * {@inheritdoc}
     */
    public function &setUsernameFormat($value)
    {
        if (in_array($value, self::VALID_USERNAME_FORMATS)) {
            $this->username_format = $value;
        } else {
            throw new InvalidArgumentException('Username format is not valid');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function rememberExtendsSession()
    {
        return $this->remember_extends_session;
    }

    /**
     * {@inheritdoc}
     */
    public function &setRememberExtendsSession($value)
    {
        $this->remember_extends_session = (bool) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordChangeEnabled()
    {
        return $this->password_change_enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function &setIsPasswordChangeEnabled($value)
    {
        $this->password_change_enabled = (bool) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordRecoveryEnabled()
    {
        return $this->password_recovery_enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function &setIsPasswordRecoveryEnabled($value)
    {
        $this->password_recovery_enabled = (bool) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalLoginUrl()
    {
        return $this->external_login_url;
    }

    /**
     * {@inheritdoc}
     */
    public function &setExternalLoginUrl($value)
    {
        $this->external_login_url = $this->getValidExternalUrlValue($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalLogoutUrl()
    {
        return $this->external_logout_url;
    }

    /**
     * {@inheritdoc}
     */
    public function &setExternalLogoutUrl($value)
    {
        $this->external_logout_url = $this->getValidExternalUrlValue($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalChangePasswordUrl()
    {
        return $this->external_change_password_url;
    }

    /**
     * {@inheritdoc}
     */
    public function &setExternalChangePasswordUrl($value)
    {
        $this->external_change_password_url = $this->getValidExternalUrlValue($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalUpdateProfileUrl()
    {
        return $this->external_update_profile_url;
    }

    /**
     * {@inheritdoc}
     */
    public function &setExternalUpdateProfileUrl($value)
    {
        $this->external_update_profile_url = $this->getValidExternalUrlValue($value);

        return $this;
    }

    /**
     * Validate and return acceptable URL value.
     *
     * @param  string|null $url
     * @return string|null
     */
    private function getValidExternalUrlValue($url)
    {
        if ($url === null || (is_string($url) && filter_var($url, FILTER_VALIDATE_URL))) {
            return $url;
        }

        throw new InvalidArgumentException('URL is not valid');
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'username_format' => $this->getUsernameFormat(),
            'remember_extends_session' => $this->rememberExtendsSession(),
            'password_change_enabled' => $this->isPasswordChangeEnabled(),
            'password_recovery_enabled' => $this->isPasswordRecoveryEnabled(),
            'external_login_url' => $this->getExternalLoginUrl(),
            'external_logout_url' => $this->getExternalLogoutUrl(),
            'external_change_password_url' => $this->getExternalChangePasswordUrl(),
            'external_update_profile_url' => $this->getExternalUpdateProfileUrl(),
        ];
    }
}
