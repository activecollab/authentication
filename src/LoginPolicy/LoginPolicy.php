<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\LoginPolicy;

use InvalidArgumentException;

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

    public function getUsernameFormat(): string
    {
        return $this->username_format;
    }

    public function setUsernameFormat(string $value): LoginPolicyInterface
    {
        if (in_array($value, self::VALID_USERNAME_FORMATS)) {
            $this->username_format = $value;
        } else {
            throw new InvalidArgumentException('Username format is not valid');
        }

        return $this;
    }

    public function rememberExtendsSession(): bool
    {
        return $this->remember_extends_session;
    }

    public function setRememberExtendsSession(bool $value): LoginPolicyInterface
    {
        $this->remember_extends_session = $value;

        return $this;
    }

    public function isPasswordChangeEnabled(): bool
    {
        return $this->password_change_enabled;
    }

    public function setIsPasswordChangeEnabled(bool $value): LoginPolicyInterface
    {
        $this->password_change_enabled = $value;

        return $this;
    }

    public function isPasswordRecoveryEnabled(): bool
    {
        return $this->password_recovery_enabled;
    }

    public function setIsPasswordRecoveryEnabled(bool $value): LoginPolicyInterface
    {
        $this->password_recovery_enabled = (bool) $value;

        return $this;
    }

    public function getExternalLoginUrl(): ?string
    {
        return $this->external_login_url;
    }

    public function setExternalLoginUrl(?string $value): LoginPolicyInterface
    {
        $this->external_login_url = $this->getValidExternalUrlValue($value);

        return $this;
    }

    public function getExternalLogoutUrl(): ?string
    {
        return $this->external_logout_url;
    }

    public function setExternalLogoutUrl(?string $value): LoginPolicyInterface
    {
        $this->external_logout_url = $this->getValidExternalUrlValue($value);

        return $this;
    }

    public function getExternalChangePasswordUrl(): ?string
    {
        return $this->external_change_password_url;
    }

    public function setExternalChangePasswordUrl(?string $value): LoginPolicyInterface
    {
        $this->external_change_password_url = $this->getValidExternalUrlValue($value);

        return $this;
    }

    public function getExternalUpdateProfileUrl(): ?string
    {
        return $this->external_update_profile_url;
    }

    public function setExternalUpdateProfileUrl(?string $value): LoginPolicyInterface
    {
        $this->external_update_profile_url = $this->getValidExternalUrlValue($value);

        return $this;
    }

    private function getValidExternalUrlValue(?string $url): ?string
    {
        if ($url === null || (is_string($url) && filter_var($url, FILTER_VALIDATE_URL))) {
            return $url;
        }

        throw new InvalidArgumentException('URL is not valid');
    }

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
