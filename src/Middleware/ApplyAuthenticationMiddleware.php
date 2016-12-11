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
     * @var bool
     */
    private $apply_on_exit;

    /**
     * @param string $request_attribute_name
     * @param bool   $apply_on_exit
     */
    public function __construct($request_attribute_name = '', $apply_on_exit = false)
    {
        $this->request_attribute_name = $request_attribute_name;
        $this->apply_on_exit = (bool) $apply_on_exit;
    }

    /**
     * @return bool
     */
    public function applyOnExit()
    {
        return $this->apply_on_exit;
    }

    /**
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @param  callable|null          $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        if (!$this->apply_on_exit) {
            list($request, $response) = $this->apply($request, $response);
        }

        if ($next) {
            $response = $next($request, $response);
        }

        if ($this->apply_on_exit) {
            $response = $this->apply($request, $response)[1];
        }

        return $response;
    }

    /**
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @return array
     */
    private function apply(ServerRequestInterface $request, ResponseInterface $response)
    {
        $transport = $this->getTransportFrom($request);

        if ($transport instanceof TransportInterface && !$transport->isEmpty() && !$transport->isApplied()) {
            list($request, $response) = $transport->applyTo($request, $response);
        }

        return [$request, $response];
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
