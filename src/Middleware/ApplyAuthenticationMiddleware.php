<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Middleware;

use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Middleware
 */
class ApplyAuthenticationMiddleware
{
    /**
     * @var string
     */
    private $request_attribute_name;

    /**
     * @param string $request_attribute_name
     */
    public function __construct($request_attribute_name = '')
    {
        $this->request_attribute_name = $request_attribute_name;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $transport = $this->getTransportFrom($request);

        if ($transport instanceof TransportInterface && !$transport->isEmpty() && !$transport->isFinalized()) {
            list($request, $response) = $transport->applyTo($request, $response);
        }

        if ($next) {
            $response = $next($request, $response);
        }

        return $response;
    }

    /**
     * Get authentication response transport from request.
     *
     * @param  ServerRequestInterface  $request
     * @return TransportInterface|null
     */
    protected function getTransportFrom(ServerRequestInterface $request)
    {
        return $this->request_attribute_name ? $request->getAttribute($this->request_attribute_name) : null;
    }
}
