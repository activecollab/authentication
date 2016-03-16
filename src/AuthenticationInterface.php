<?php

/*
 * This file is part of the Active Collab ID project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @package ActiveCollab\Authentication
 */
interface AuthenticationInterface
{
    /**
     * Initialize authentication layer and see if we have a user who's already logged in.
     *
     * @param  RequestInterface                $request
     * @return AuthenticatedUserInterface|null
     */
    public function initialize(RequestInterface $request);

    /**
     * Authenticate with given credential agains authentication source.
     *
     * @param  RequestInterface           $request
     * @return AuthenticatedUserInterface
     */
    public function authenticate(RequestInterface $request);
}
