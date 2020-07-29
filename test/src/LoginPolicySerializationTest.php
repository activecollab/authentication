<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\LoginPolicy\LoginPolicy;
use ActiveCollab\Authentication\LoginPolicy\LoginPolicyInterface;
use ActiveCollab\Authentication\Test\TestCase\TestCase;
use JsonSerializable;

/**
 * @package ActiveCollab\Authentication\Test
 */
class LoginPolicySerializationTest extends TestCase
{
    public function testLoginPolicyCanBeSerialized()
    {
        $this->assertInstanceOf(JsonSerializable::class, new LoginPolicy());
    }

    public function testJsonSerialization()
    {
        $default_policy = new LoginPolicy();

        $data = json_decode(json_encode($default_policy), true);
        $this->assertIsArray($data);

        $this->assertArrayHasKey('username_format', $data);
        $this->assertArrayHasKey('remember_extends_session', $data);
        $this->assertArrayHasKey('password_change_enabled', $data);
        $this->assertArrayHasKey('password_recovery_enabled', $data);
        $this->assertArrayHasKey('external_login_url', $data);
        $this->assertArrayHasKey('external_logout_url', $data);
        $this->assertArrayHasKey('external_change_password_url', $data);
        $this->assertArrayHasKey('external_update_profile_url', $data);

        $this->assertSame(LoginPolicyInterface::USERNAME_FORMAT_TEXT, $data['username_format']);
        $this->assertSame(true, $data['remember_extends_session']);
        $this->assertSame(true, $data['password_change_enabled']);
        $this->assertSame(true, $data['password_recovery_enabled']);
        $this->assertSame(null, $data['external_login_url']);
        $this->assertSame(null, $data['external_logout_url']);
        $this->assertSame(null, $data['external_change_password_url']);
        $this->assertSame(null, $data['external_update_profile_url']);

        $customized_policy = new LoginPolicy(LoginPolicyInterface::USERNAME_FORMAT_EMAIL, false, false, false, 'http://google.com/login', 'http://google.com/logout', 'http://google.com/change-password', 'http://google.com/update-profile');

        $data = json_decode(json_encode($customized_policy), true);
        $this->assertIsArray($data);

        $this->assertArrayHasKey('username_format', $data);
        $this->assertArrayHasKey('remember_extends_session', $data);
        $this->assertArrayHasKey('password_change_enabled', $data);
        $this->assertArrayHasKey('password_recovery_enabled', $data);
        $this->assertArrayHasKey('external_login_url', $data);
        $this->assertArrayHasKey('external_logout_url', $data);
        $this->assertArrayHasKey('external_change_password_url', $data);
        $this->assertArrayHasKey('external_update_profile_url', $data);

        $this->assertSame(LoginPolicyInterface::USERNAME_FORMAT_EMAIL, $data['username_format']);
        $this->assertSame(false, $data['remember_extends_session']);
        $this->assertSame(false, $data['password_change_enabled']);
        $this->assertSame(false, $data['password_recovery_enabled']);
        $this->assertSame('http://google.com/login', $data['external_login_url']);
        $this->assertSame('http://google.com/logout', $data['external_logout_url']);
        $this->assertSame('http://google.com/change-password', $data['external_change_password_url']);
        $this->assertSame('http://google.com/update-profile', $data['external_update_profile_url']);
    }
}
