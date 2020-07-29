<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\TestCase;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;

abstract class RequestResponseTestCase extends BaseTestCase
{
    /**
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    public function setUp(): void
    {
        parent::setUp();

        $this->request = (new ServerRequestFactory())->createServerRequest(
            'GET',
            'https://example.com:443/foo/bar?abc=123'
        );
        $this->response = (new ResponseFactory())->createResponse();
    }
}
