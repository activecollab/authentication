<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Exception\InvalidPasswordException;
use ActiveCollab\Authentication\Password\Policy\PasswordPolicy;
use ActiveCollab\Authentication\Password\StrengthValidator\PasswordStrengthValidator;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

class PasswordStrengthValidatorTest extends TestCase
{
    public function testPasswordNeedsToBeString()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage("Password is not a string");

        (new PasswordStrengthValidator())->validate(1234567, new PasswordPolicy());
    }

    public function testEmptyPasswordIsInvalid()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage("Password is empty");

        (new PasswordStrengthValidator())->validate('', new PasswordPolicy());
    }

    public function testPasswordIsTrimmedWhenTestedIfItIsEmpty()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage("Password is empty");

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
        $this->assertTrue(
            (new PasswordStrengthValidator())
                ->validate(
                    'BhkXuemYY#WMdU;QQd4QpXpcEjbw2XHP',
                    new PasswordPolicy(
                        32,
                        true,
                        true,
                        true
                    )
                )
        );
    }

    public function testLengthValidation()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage("Password is not strong enough");
        $this->expectExceptionCode(1);

        (new PasswordStrengthValidator())->validate('short', new PasswordPolicy(15));
    }

    public function testNoNumbers()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage("Password is not strong enough");
        $this->expectExceptionCode(2);

        (new PasswordStrengthValidator())
            ->validate('short', new PasswordPolicy(0, true));
    }

    public function testNoMixetCase()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage("Password is not strong enough");
        $this->expectExceptionCode(4);

        (new PasswordStrengthValidator())
            ->validate('flat', new PasswordPolicy(0, false, true));
    }

    public function testNoSymbols()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage("Password is not strong enough");
        $this->expectExceptionCode(8);

        (new PasswordStrengthValidator())
            ->validate('flat', new PasswordPolicy(0, false, false, true));
    }

    public function testCombination()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage("Password is not strong enough");
        $this->expectExceptionCode(15);

        (new PasswordStrengthValidator())
            ->validate('flat', new PasswordPolicy(15, true, true, true));
    }
}
