<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\AuthenticationResult\Transport;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface TransportInterface
{
    public function getAdapter(): AdapterInterface;

    public function getPayload();
    public function setPayload($value): TransportInterface;

    public function isEmpty(): bool;

    public function applyToRequest(ServerRequestInterface $request): ServerRequestInterface;
    public function applyToResponse(ResponseInterface $response): ResponseInterface;
    public function applyTo(ServerRequestInterface $request, ResponseInterface $response): array;

    public function isApplied(): bool;
    public function isAppliedToRequest(): bool;
    public function isAppliedToResponse(): bool;
}
