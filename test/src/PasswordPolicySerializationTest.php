<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Password\Policy\PasswordPolicy;
use ActiveCollab\Authentication\Test\TestCase\TestCase;
use JsonSerializable;

class PasswordPolicySerializationTest extends TestCase
{
    public function testPasswordPolicyCanBeSerialized()
    {
        $this->assertInstanceOf(JsonSerializable::class, new PasswordPolicy());
    }

    public function testJsonSerialization()
    {
        $default_policy = new PasswordPolicy();

        $data = json_decode(json_encode($default_policy), true);
        $this->assertIsArray($data);

        $this->assertArrayHasKey('min_length', $data);
        $this->assertArrayHasKey('require_numbers', $data);
        $this->assertArrayHasKey('require_mixed_case', $data);
        $this->assertArrayHasKey('require_symbols', $data);

        $this->assertSame(0, $data['min_length']);
        $this->assertSame(false, $data['require_numbers']);
        $this->assertSame(false, $data['require_mixed_case']);
        $this->assertSame(false, $data['require_symbols']);

        $customized_policy = new PasswordPolicy(
            32,
            true,
            true,
            true
        );

        $data = json_decode(json_encode($customized_policy), true);

        $this->assertArrayHasKey('min_length', $data);
        $this->assertArrayHasKey('require_numbers', $data);
        $this->assertArrayHasKey('require_mixed_case', $data);
        $this->assertArrayHasKey('require_symbols', $data);

        $this->assertSame(32, $data['min_length']);
        $this->assertSame(true, $data['require_numbers']);
        $this->assertSame(true, $data['require_mixed_case']);
        $this->assertSame(true, $data['require_symbols']);
    }
}
