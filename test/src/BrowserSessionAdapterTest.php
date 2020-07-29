<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\BrowserSessionTestCase;
use InvalidArgumentException;

class BrowserSessionAdapterTest extends BrowserSessionTestCase
{
    public function testCookieNameIsRequired()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Session cookie name is required");

        new BrowserSessionAdapter(
            $this->empty_user_repository,
            $this->empty_session_repository,
            $this->cookies,
            ''
        );
    }
}
