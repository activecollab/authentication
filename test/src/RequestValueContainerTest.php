<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Test\TestCase\RequestResponseTestCase;
use ActiveCollab\Authentication\ValueContainer\RequestValueContainer;

class RequestValueContainerTest extends RequestResponseTestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Request not set.
     */
    public function testHasValueWithNoRequest()
    {
        (new RequestValueContainer('test_key'))->hasValue();
    }

    public function testHasValue()
    {
        $container = new RequestValueContainer('test_key');
        $container->setRequest($this->request);

        $this->assertFalse($container->hasValue());

        $this->request = $this->request->withAttribute('test_key', 123);
        $container->setRequest($this->request);

        $this->assertTrue($container->hasValue());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Request not set.
     */
    public function testGetValueWithNoRequest()
    {
        (new RequestValueContainer('test_key'))->getValue();
    }

    public function testGetValue()
    {
        $container = new RequestValueContainer('test_key');
        $container->setRequest($this->request);

        $this->assertNull($container->getValue());

        $this->request = $this->request->withAttribute('test_key', 123);
        $container->setRequest($this->request);

        $this->assertSame(123, $container->getValue());
    }
}
