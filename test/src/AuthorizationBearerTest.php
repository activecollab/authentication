<?php

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\AuthorizationBearer;
use ActiveCollab\Authentication\Test\Base\TestCase;
use ActiveCollab\Authentication\Test\Http\Request;

/**
 * @package ActiveCollab\Authentication\Test
 */
class AuthorizationBearerTest extends TestCase
{
    /**
     * Test if we can properly set header line using our stub request objects
     */
    public function testAuthorizationBearerTest()
    {
        $this->assertEquals('Bearer 123', (new Request())->withHeader('Authorization', 'Bearer 123')->getHeaderLine('Authorization'));
    }

    /**
     * Test if adapter passes through when there's no authroization bearer header
     */
    public function testInitializationSkipWhenTheresNoAuthroizationHeader()
    {
        $this->assertNull((new AuthorizationBearer())->initialize(new Request()));
    }

    /**
     * Test if adapter passes through when there's authroization bearer header, but it's not token bearer
     */
    public function testInitializationSkipWhenAuthorizationIsNotTokenBearer()
    {
        $this->assertNull((new AuthorizationBearer())->initialize((new Request())->withHeader('Authorization', 'Basic 123')));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidTokenException
     */
    public function testExceptionWhenTokenIsNotValid()
    {
        (new AuthorizationBearer())->initialize((new Request())->withHeader('Authorization', 'Bearer 123'));
    }

//    public function testExceptionWhenTokenIsExpired()
//    {
//
//    }
//
//    public function testAuthorisationWithGoodToken()
//    {
//
//    }
}
