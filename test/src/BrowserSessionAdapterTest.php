<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\Test\Session\Session;
use ActiveCollab\Authentication\Test\TestCase\BrowserSessionTestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class BrowserSessionAdapterTest extends BrowserSessionTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Session cookie name is required
     */
    public function testCookieNameIsRequired()
    {
        new BrowserSessionAdapter($this->empty_user_repository, $this->empty_session_repository, $this->cookies, '');
    }
}
