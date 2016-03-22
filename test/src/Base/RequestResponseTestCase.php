<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Base;

use Pimple\Container;
use Slim\CallableResolver;
use Slim\Handlers\Strategies\RequestResponse;
use Slim\Http\Body;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

/**
 * @package ActiveCollab\Authentication\Test\Base
 */
abstract class RequestResponseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Set up the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $uri = Uri::createFromString('https://example.com:443/foo/bar?abc=123');
        $this->request = new Request('GET', $uri, new Headers(), [], Environment::mock()->all(), new Body(fopen('php://temp', 'r+')));
        $this->response = new Response();

        $this->container = new Container();

        $this->container['callableResolver'] = function ($c) {
            return new CallableResolver($c);
        };
        $this->container['foundHandler'] = function () {
            return new RequestResponse();
        };
    }
}
