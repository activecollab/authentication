<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Password\Manager\PasswordManager;
use ActiveCollab\Authentication\Password\Manager\PasswordManagerInterface;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class PasswordManagerTest extends TestCase
{
    public function testVerifyExceptionOnInvalidMechanism()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Hashing mechanism 'unknown hash algo' is not supported");

        (new PasswordManager())->verify('123', '1234567890', 'unknown hash algo');
    }

    public function testHashExceptionOnInvalidMechanism()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Hashing mechanism 'unknown hash algo' is not supported");

        (new PasswordManager())->hash('123', 'unknown hash algo');
    }

    /**
     * Test if PHP password hashing works as expected.
     */
    public function testPhp()
    {
        $manager = new PasswordManager('salt');

        $hash = $manager->hash('123', PasswordManagerInterface::HASHED_WITH_PHP);

        $this->assertIsString($hash);
        $this->assertGreaterThan(40, strlen($hash));

        $this->assertTrue($manager->verify('123', $hash, PasswordManagerInterface::HASHED_WITH_PHP));
    }

    /**
     * Test if PBKDF2 hashing works as expected.
     */
    public function testPbkdf2()
    {
        $manager = new PasswordManager('salt');

        $hash = $manager->hash('123', PasswordManagerInterface::HASHED_WITH_PBKDF2);

        $this->assertIsString($hash);
        $this->assertGreaterThan(40, strlen($hash));
        $this->assertStringEndsWith('==', $hash);

        $this->assertTrue($manager->verify('123', $hash, PasswordManagerInterface::HASHED_WITH_PBKDF2));
    }

    /**
     * Test if SHA1 hashing works as expected.
     */
    public function testSha1()
    {
        $manager = new PasswordManager('salt');

        $hash = $manager->hash('123', PasswordManagerInterface::HASHED_WITH_SHA1);

        $this->assertIsString($hash);
        $this->assertEquals(40, strlen($hash));

        $this->assertTrue($manager->verify('123', $hash, PasswordManagerInterface::HASHED_WITH_SHA1));
    }

    /**
     * Test if PBKDF2 and SHA1 hashed password always recommend rehashing (using PHP password hashing system).
     */
    public function testSha1AndPbkdf2NeedRehash()
    {
        $manager = new PasswordManager('salt');

        $this->assertFalse($manager->needsRehash($manager->hash('123', PasswordManagerInterface::HASHED_WITH_PHP), PasswordManagerInterface::HASHED_WITH_PHP));
        $this->assertTrue($manager->needsRehash($manager->hash('123', PasswordManagerInterface::HASHED_WITH_PBKDF2), PasswordManagerInterface::HASHED_WITH_PBKDF2));
        $this->assertTrue($manager->needsRehash($manager->hash('123', PasswordManagerInterface::HASHED_WITH_SHA1), PasswordManagerInterface::HASHED_WITH_SHA1));
    }
}
