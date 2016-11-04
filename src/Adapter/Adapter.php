<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Transport;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
abstract class Adapter implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function finishInitialization(ServerRequestInterface $request, ResponseInterface $response, AuthenticatedUserInterface $authenticated_user = null, AuthenticationResultInterface $authenticated_with = null, array $additional_arguments = [])
    {
        return new Transport($request, $response, $authenticated_user, $authenticated_with, $additional_arguments);
    }
}
