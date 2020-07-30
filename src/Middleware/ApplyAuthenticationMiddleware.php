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
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApplyAuthenticationMiddleware implements MiddlewareInterface
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
            [$request, $response] = $this->apply($request, $response);
        }

        if ($next) {
            $response = $next($request, $response);
        }

        if ($this->apply_on_exit) {
            $response = $this->apply($request, $response)[1];
        }

        return $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->apply_on_exit) {
            $request = $this->applyToRequest($request);
        }

        $response = $handler->handle($request);
        $response = $this->applyToResponse($request, $response);

        return $response;
    }

    private function applyToRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        $transport = $this->getTransportFrom($request);

        if ($transport instanceof TransportInterface
            && !$transport->isEmpty()
            && !$transport->isAppliedToResponse()
        ) {
            return $transport->applyToRequest($request);
        }

        return $request;
    }

    private function applyToResponse(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface
    {
        $transport = $this->getTransportFrom($request);

        if ($transport instanceof TransportInterface
            && !$transport->isEmpty()
            && !$transport->isAppliedToResponse()
        ) {
            return $transport->applyToResponse($response);
        }

        return $response;
    }

    private function apply(ServerRequestInterface $request, ResponseInterface $response): array
    {
        $request = $this->applyToRequest($request);
        $response = $this->applyToResponse($request, $response);

        return [
            $request,
            $response,
        ];
    }

    protected function getTransportFrom(ServerRequestInterface $request): ?TransportInterface
    {
        if ($this->value_container instanceof RequestValueContainerInterface) {
            $this->value_container->setRequest($request);
        }

        if ($this->value_container->hasValue()) {
            $transport = $this->value_container->getValue();

            if ($transport instanceof TransportInterface) {
                return $transport;
            }
        }

        return null;
    }
}
