<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearerAdapter;
use ActiveCollab\Authentication\Authentication;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Fixtures\Authorizer;
use ActiveCollab\Authentication\Test\TestCase\RequestResponseTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;
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
