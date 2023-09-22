<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Password\Manager\PasswordManager;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

class PasswordManagerTest extends TestCase
{
    public function testHashWithPhp()
    {
        $manager = new PasswordManager('salt');

        $hash = $manager->hash('123');

        $this->assertIsString($hash);
        $this->assertGreaterThan(40, strlen($hash));

        $this->assertTrue($manager->verify('123', $hash));
    }
}
