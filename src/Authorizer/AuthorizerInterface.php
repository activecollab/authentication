<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

interface AuthorizerInterface
{
    /**
     * Perform user credentials verification against the real user database provider.
     *
     * @param  array      $payload
     * @return mixed|null
     */
    public function verifyCredentials(array $payload);

    /**
     * Send an event to the real user database provider when user is logged in.
     * @param  array      $payload
     * @return mixed|null
     */
    public function onLogin(array $payload);

    /**
     * Send an event to the real user database provider when user is logged out.
     * @param  array      $payload
     * @return mixed|null
     */
    public function onLogout(array $payload);
}
