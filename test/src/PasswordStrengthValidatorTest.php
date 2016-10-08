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
class PasswordStrengthValidatorTest extends TestCase
{
    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not a string
     */
    public function testPasswordNeedsToBeString()
    {
        (new PasswordStrengthValidator())->validate(1234567, new PasswordPolicy());
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is empty
     */
    public function testEmptyPasswordIsInvalid()
    {
        (new PasswordStrengthValidator())->validate('', new PasswordPolicy());
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is empty
     */
    public function testPasswordIsTrimmedWhenTestedIfItIsEmpty()
    {
        (new PasswordStrengthValidator())->validate('     ', new PasswordPolicy());
    }

    /**
     * Test OK validation with weak policy.
     */
    public function testWeakPasswordOk()
    {
        $this->assertTrue((new PasswordStrengthValidator())->validate('weak', new PasswordPolicy()));
    }

    /**
     * Test OK validation with strong policy.
     */
    public function testStrongPasswordOk()
    {
        $this->assertTrue((new PasswordStrengthValidator())->validate('BhkXuemYY#WMdU;QQd4QpXpcEjbw2XHP', new PasswordPolicy(32, true, true, true)));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not strong enough
     * @expectedExceptionCode 1
     */
    public function testLengthValidation()
    {
        (new PasswordStrengthValidator())->validate('short', new PasswordPolicy(15));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not strong enough
     * @expectedExceptionCode 2
     */
    public function testNoNumbers()
    {
        (new PasswordStrengthValidator())->validate('short', new PasswordPolicy(0, true));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not strong enough
     * @expectedExceptionCode 4
     */
    public function testNoMixetCase()
    {
        (new PasswordStrengthValidator())->validate('flat', new PasswordPolicy(0, false, true));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not strong enough
     * @expectedExceptionCode 8
     */
    public function testNoSymbols()
    {
        (new PasswordStrengthValidator())->validate('flat', new PasswordPolicy(0, false, false, true));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not strong enough
     * @expectedExceptionCode 15
     */
    public function testCombination()
    {
        (new PasswordStrengthValidator())->validate('flat', new PasswordPolicy(15, true, true, true));
    }
}
