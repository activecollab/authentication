<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface AdapterInterface
{
    public function initialize(ServerRequestInterface $request): ?TransportInterface;

    public function applyToRequest(
        ServerRequestInterface $request,
        TransportInterface $transport
    ): ServerRequestInterface;

    public function applyToResponse(
        ResponseInterface $response,
        TransportInterface $transport
    ): ResponseInterface;

    public function applyTo(
        ServerRequestInterface $request,
        ResponseInterface $response,
        TransportInterface $transport
    ): array;

    public function authenticate(
        AuthenticatedUserInterface $authenticated_user,
        array $credentials = []
    ): AuthenticationResultInterface;

    public function terminate(AuthenticationResultInterface $authenticated_with): TransportInterface;
}
