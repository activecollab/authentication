<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\LoginPolicy;

use JsonSerializable;

interface LoginPolicyInterface extends JsonSerializable
{
    const USERNAME_FORMAT_TEXT = 'text';
    const USERNAME_FORMAT_EMAIL = 'email';

    const VALID_USERNAME_FORMATS = [
        self::USERNAME_FORMAT_TEXT,
        self::USERNAME_FORMAT_EMAIL,
    ];

    public function getUsernameFormat(): string;
    public function setUsernameFormat(string $value): LoginPolicyInterface;

    public function rememberExtendsSession(): bool;
    public function setRememberExtendsSession(bool $value): LoginPolicyInterface;

    public function isPasswordChangeEnabled(): bool;
    public function setIsPasswordChangeEnabled(bool $value): LoginPolicyInterface;

    public function isPasswordRecoveryEnabled(): bool;
    public function setIsPasswordRecoveryEnabled(bool $value): LoginPolicyInterface;

    public function getExternalLoginUrl(): ?string;
    public function setExternalLoginUrl(?string $value): LoginPolicyInterface;

    public function getExternalLogoutUrl(): ?string;
    public function setExternalLogoutUrl(?string $value): LoginPolicyInterface;

    public function getExternalChangePasswordUrl(): ?string;
    public function setExternalChangePasswordUrl(?string $value): LoginPolicyInterface;

    public function getExternalUpdateProfileUrl(): ?string;
    public function setExternalUpdateProfileUrl(?string $value): LoginPolicyInterface;
}
