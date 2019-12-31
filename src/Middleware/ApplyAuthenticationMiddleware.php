<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Middleware;

use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\ValueContainer\Request\RequestValueContainerInterface;
use ActiveCollab\ValueContainer\ValueContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApplyAuthenticationMiddleware
{
    private $value_container;
    private $apply_on_exit;

    public function __construct(ValueContainerInterface $value_container, bool $apply_on_exit = false)
    {
        $this->value_container = $value_container;
        $this->apply_on_exit = (bool) $apply_on_exit;
    }

    public function applyOnExit(): bool
    {
        return $this->apply_on_exit;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ): ResponseInterface
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

    private function apply(ServerRequestInterface $request, ResponseInterface $response): array
    {
        $transport = $this->getTransportFrom($request);

        if ($transport instanceof TransportInterface && !$transport->isEmpty() && !$transport->isApplied()) {
            list($request, $response) = $transport->applyTo($request, $response);
        }

        return [$request, $response];
    }

    protected function getTransportFrom(ServerRequestInterface $request): ?TransportInterface
    {
        if ($this->value_container instanceof RequestValueContainerInterface) {
            $this->value_container->setRequest($request);
        }

        if ($this->value_container->hasValue()) {
            return $this->value_container->getValue();
        }

        return null;
    }
}
