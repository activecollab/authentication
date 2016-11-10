<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Authentication;
use ActiveCollab\Authentication\Test\TestCase\RequestResponseTestCase;
use stdClass;

class AuthenticationTest extends RequestResponseTestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid object type provided
     */
    public function testForInvalidAdapterExceptionIsThrown()
    {
        new Authentication([new stdClass()]);
    }
}
