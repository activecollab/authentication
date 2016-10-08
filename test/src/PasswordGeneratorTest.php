<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Password\Policy\PasswordPolicy;
use ActiveCollab\Authentication\Password\StrengthValidator\PasswordStrengthValidator;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class PasswordGeneratorTest extends TestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Minimal password length that this utility can generate is 16 characters
     */
    public function testMinPasswordLength()
    {
        (new PasswordStrengthValidator())->generateValid(8, new PasswordPolicy());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Password policy requires longer password
     */
    public function testPasswordLengthCantBeShorterThanPolicyLength()
    {
        (new PasswordStrengthValidator())->generateValid(18, new PasswordPolicy(32));
    }

    /**
     * Test valid password generation (with symbols).
     */
    public function testSuccessfulPasswordGenereationWithSimbols()
    {
        $validator = new PasswordStrengthValidator();
        $policy = new PasswordPolicy(32, true, true, true);

        $password = $validator->generateValid(32, $policy);

        $this->assertInternalType('string', $password);
        $this->assertNotEmpty($password);
        $this->assertFalse(ctype_alnum($password));

        $this->assertTrue($validator->validate($password, $policy));
    }

    /**
     * Test valid password generation (without symbols).
     */
    public function testSuccessfulPasswordGenereationWithoutSimbols()
    {
        $validator = new PasswordStrengthValidator();
        $policy = new PasswordPolicy(32, true, true, false);

        $password = $validator->generateValid(32, $policy);

        $this->assertInternalType('string', $password);
        $this->assertNotEmpty($password);
        $this->assertTrue(ctype_alnum($password));

        $this->assertTrue($validator->validate($password, $policy));
    }
}
