<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @package ActiveCollab\Authentication
 */
interface AuthenticationInterface
{
    /**
     * Initialize authentication layer and see if we have a user who's already logged in.
     *
     * @param  RequestInterface $request
     * @return RequestInterface
     */
    public function initialize(RequestInterface $request);

    /**
     * Set Authorizer object.
     *
     * @param AuthorizerInterface $authorizer
     */
    public function setAuthorizer(AuthorizerInterface $authorizer);

    /**
     * Authenticate with given credential agains authentication source.
     *
     * @param  RequestInterface           $request
     * @param  array                      $credentials
     * @return AuthenticatedUserInterface
     */
    public function authorize(RequestInterface $request, array $credentials = []);
}
