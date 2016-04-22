<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
interface AdapterInterface
{
    /**
     * Initialize authentication layer and see if we have a user who's already logged in.
     *
     * @param  ServerRequestInterface          $request
     * @return AuthenticatedUserInterface|null
     */
    public function initialize(ServerRequestInterface $request);

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
     * @param AuthenticationResultInterface $authentication_result
     */
    public function terminate(AuthenticationResultInterface $authentication_result);
}
