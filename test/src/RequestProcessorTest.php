<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessingResult\RequestProcessingResult;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

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
