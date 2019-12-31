<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransportInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authorization\AuthorizationTransportInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Adapter implements AdapterInterface
{

    public function applyTo(
        ServerRequestInterface $request,
        ResponseInterface $response,
        TransportInterface $transport
    ): array
    {
        if ($transport instanceof AuthenticationTransportInterface
            || $transport instanceof AuthorizationTransportInterface
        ) {
            $request = $request
                ->withAttribute('authentication_adapter', $this)
                ->withAttribute('authenticated_user', $transport->getAuthenticatedUser())
                ->withAttribute('authenticated_with', $transport->getAuthenticatedWith());
        }

        return [$request, $response];
    }
}
