<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessingResult\RequestProcessingResult;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class RequestProcessorTest extends TestCase
{
    public function testDefaultPayloadIsNull()
    {
        $this->assertNull((new RequestProcessingResult([]))->getDefaultPayload());
    }

    public function testConstructRequestProcessingResult()
    {
        $credentials = [
            'username' => 'me',
            'password' => 'hard to guess, easy to remember',
        ];

        $payload = [1, 2, 3];

        $result = new RequestProcessingResult($credentials, $payload);

        $this->assertSame($credentials, $result->getCredentials());
        $this->assertSame($payload, $result->getDefaultPayload());
    }
}
