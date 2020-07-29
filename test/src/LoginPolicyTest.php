<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\LoginPolicy\LoginPolicy;
use ActiveCollab\Authentication\LoginPolicy\LoginPolicyInterface;
use ActiveCollab\Authentication\Test\TestCase\TestCase;
use InvalidArgumentException;

class LoginPolicyTest extends TestCase
{
    public function testInvalidUsernameFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Username format is not valid");

        new LoginPolicy('unknown format');
    }

    /**
     * Test valid username formats are accepted.
     */
    public function testValidUsernameFormat()
    {
        $this->assertSame(
            LoginPolicyInterface::USERNAME_FORMAT_TEXT,
            (new LoginPolicy(LoginPolicyInterface::USERNAME_FORMAT_TEXT))->getUsernameFormat()
        );
        $this->assertSame(
            LoginPolicyInterface::USERNAME_FORMAT_EMAIL,
            (new LoginPolicy(LoginPolicyInterface::USERNAME_FORMAT_EMAIL))->getUsernameFormat()
        );
    }

//    /**
//     * Test if flags are properly cast to boolean values.
//     */
//    public function testFlagsAreCasterToBool()
//    {
//        $this->assertSame(true, (new LoginPolicy())->setRememberExtendsSession('1')->rememberExtendsSession());
//        $this->assertSame(true, (new LoginPolicy())->setIsPasswordChangeEnabled(1)->isPasswordChangeEnabled());
//        $this->assertSame(true, (new LoginPolicy())->setIsPasswordRecoveryEnabled('YES')->isPasswordRecoveryEnabled());
//
//        $this->assertSame(false, (new LoginPolicy())->setRememberExtendsSession('0')->rememberExtendsSession());
//        $this->assertSame(false, (new LoginPolicy())->setIsPasswordChangeEnabled(0)->isPasswordChangeEnabled());
//        $this->assertSame(false, (new LoginPolicy())->setIsPasswordRecoveryEnabled('0')->isPasswordRecoveryEnabled());
//    }

    /**
     * Test if system accepts URLs or NULL.
     */
    public function testValidUrlsOrNull()
    {
        $this->assertSame('https://www.activecollab.com/login', (new LoginPolicy())->setExternalLoginUrl('https://www.activecollab.com/login')->getExternalLoginUrl());
        $this->assertSame('https://www.activecollab.com/logout', (new LoginPolicy())->setExternalLogoutUrl('https://www.activecollab.com/logout')->getExternalLogoutUrl());
        $this->assertSame('https://www.activecollab.com/password', (new LoginPolicy())->setExternalChangePasswordUrl('https://www.activecollab.com/password')->getExternalChangePasswordUrl());
        $this->assertSame('https://www.activecollab.com/profile', (new LoginPolicy())->setExternalUpdateProfileUrl('https://www.activecollab.com/profile')->getExternalUpdateProfileUrl());

        $this->assertNull((new LoginPolicy())->setExternalLoginUrl(null)->getExternalLoginUrl());
        $this->assertNull((new LoginPolicy())->setExternalLogoutUrl(null)->getExternalLogoutUrl());
        $this->assertNull((new LoginPolicy())->setExternalChangePasswordUrl(null)->getExternalChangePasswordUrl());
        $this->assertNull((new LoginPolicy())->setExternalUpdateProfileUrl(null)->getExternalUpdateProfileUrl());
    }

    public function testInvalidExternalLoginUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("URL is not valid");

        (new LoginPolicy())->setExternalLoginUrl('invalid url');
    }

    public function testInvalidExternaLogoutUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("URL is not valid");

        (new LoginPolicy())->setExternalLoginUrl('invalid url');
    }

    public function testInvalidExternaChangePassword()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("URL is not valid");

        (new LoginPolicy())->setExternalChangePasswordUrl('invalid url');
    }

    public function testInvalidExternalUpdateProfile()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("URL is not valid");

        (new LoginPolicy())->setExternalUpdateProfileUrl('invalid url');
    }
}
