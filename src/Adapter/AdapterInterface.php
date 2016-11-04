<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\Adapter\InitializationResult\InitializationResultInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
interface AdapterInterface
{
    /**
     * Initialize authentication layer and see if we have a user who's already logged in.
     *
     * @param  ServerRequestInterface        $request
     * @return InitializationResultInterface
     */
    public function initialize(ServerRequestInterface $request);

    /**
     * Finish initialization once adapter which did the authentication is known (and is the only one).
     *
     * @param  ServerRequestInterface        $request
     * @param  ResponseInterface             $response
     * @param  AuthenticatedUserInterface    $authenticated_user
     * @param  AuthenticationResultInterface $authenticated_with
     * @param  array                         $additional_arguments
     * @return TransportInterface
     */
    public function finishInitialization(ServerRequestInterface $request, ResponseInterface $response, AuthenticatedUserInterface $authenticated_user = null, AuthenticationResultInterface $authenticated_with = null, array $additional_arguments = []);

    /**
     * Authenticate user against authentication source.
     *
     * @param  AuthenticatedUserInterface    $authenticated_user
     * @return AuthenticationResultInterface
     */
    public function authenticate(AuthenticatedUserInterface $authenticated_user);

    /**
     * Terminate an instance that was used to authenticate a user.
     *
     * @param AuthenticationResultInterface $authenticated_with
     */
    public function terminate(AuthenticationResultInterface $authenticated_with);
}
