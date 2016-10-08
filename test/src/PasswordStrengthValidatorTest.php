<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Password\StrengthValidator\PasswordStrengthValidator;
use ActiveCollab\Authentication\Test\Fixtures\PasswordPolicy;
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
        (new PasswordStrengthValidator())->isPasswordValid(1234567, new PasswordPolicy());
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is empty
     */
    public function testEmptyPasswordIsInvalid()
    {
        (new PasswordStrengthValidator())->isPasswordValid('', new PasswordPolicy());
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is empty
     */
    public function testPasswordIsTrimmedWhenTestedIfItIsEmpty()
    {
        (new PasswordStrengthValidator())->isPasswordValid('     ', new PasswordPolicy());
    }

    /**
     * Test OK validation with weak policy.
     */
    public function testWeakPasswordOk()
    {
        $this->assertTrue((new PasswordStrengthValidator())->isPasswordValid('weak', new PasswordPolicy()));
    }

    /**
     * Test OK validation with strong policy.
     */
    public function testStrongPasswordOk()
    {
        $this->assertTrue((new PasswordStrengthValidator())->isPasswordValid('BhkXuemYY#WMdU;QQd4QpXpcEjbw2XHP', new PasswordPolicy(32, true, true, true)));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not strong enough
     * @expectedExceptionCode 1
     */
    public function testLengthValidation()
    {
        (new PasswordStrengthValidator())->isPasswordValid('short', new PasswordPolicy(15));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not strong enough
     * @expectedExceptionCode 2
     */
    public function testNoNumbers()
    {
        (new PasswordStrengthValidator())->isPasswordValid('short', new PasswordPolicy(0, true));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not strong enough
     * @expectedExceptionCode 4
     */
    public function testNoMixetCase()
    {
        (new PasswordStrengthValidator())->isPasswordValid('flat', new PasswordPolicy(0, false, true));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not strong enough
     * @expectedExceptionCode 8
     */
    public function testNoSymbols()
    {
        (new PasswordStrengthValidator())->isPasswordValid('flat', new PasswordPolicy(0, false, false, true));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPasswordException
     * @expectedExceptionMessage Password is not strong enough
     * @expectedExceptionCode 15
     */
    public function testCombination()
    {
        (new PasswordStrengthValidator())->isPasswordValid('flat', new PasswordPolicy(15, true, true, true));
    }
}
