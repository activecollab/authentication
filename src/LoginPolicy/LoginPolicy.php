<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\LoginPolicy;

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
     * @param string $username_format
     * @param bool   $remember_extends_session
     * @param bool   $password_change_enabled
     * @param bool   $password_recovery_enabled
     * @param string $external_login_url
     * @param string $external_logout_url
     * @param string $external_change_password_url
     * @param string $external_update_profile_url
     */
    public function __construct($username_format, $remember_extends_session, $password_change_enabled, $password_recovery_enabled, $external_login_url, $external_logout_url, $external_change_password_url, $external_update_profile_url)
    {
        $this->username_format = $username_format;
        $this->remember_extends_session = (bool) $remember_extends_session;
        $this->password_change_enabled = (bool) $password_change_enabled;
        $this->password_recovery_enabled = (bool) $password_recovery_enabled;
        $this->external_login_url = $external_login_url;
        $this->external_logout_url = $external_logout_url;
        $this->external_change_password_url = $external_change_password_url;
        $this->external_update_profile_url = $external_update_profile_url;
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
    public function rememberExtendsSession()
    {
        return $this->remember_extends_session;
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
    public function isPasswordRecoveryEnabled()
    {
        return $this->password_recovery_enabled;
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
    public function getExternalLogoutUrl()
    {
        return $this->external_logout_url;
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
    public function getExternalUpdateProfileUrl()
    {
        return $this->external_update_profile_url;
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
