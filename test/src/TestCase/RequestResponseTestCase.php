<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\TestCase;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;

abstract class RequestResponseTestCase extends BaseTestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Set up the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com:443/foo/bar?abc=123'
        );
        $this->response = (new ResponseFactory())->createResponse();
        $this->container = new Container();
    }
}
