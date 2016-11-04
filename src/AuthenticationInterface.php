<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication
 */
interface AuthenticationInterface
{
    /**
     * Initialize authentication layer and see if we have a user who's already logged in.
     *
     * @param  RequestInterface|ServerRequestInterface $request
     * @return TransportInterface|null
     */
    public function initialize(ServerRequestInterface $request);

    /**
     * Finalize authentication and apply data from the transport to the response (and optionally request).
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @param  TransportInterface     $authentication_result
     * @return array
     */
    public function finalize(ServerRequestInterface $request, ResponseInterface $response, TransportInterface $authentication_result);

    /**
     * Authorize and authenticate with given credentials against authorization/authentication source.
     *
     * @param  AuthorizerInterface        $authorizer
     * @param  AdapterInterface           $adapter
     * @param  array                      $credentials
     * @return AuthenticatedUserInterface
     */
    public function authorize(AuthorizerInterface $authorizer, AdapterInterface $adapter, array $credentials);

    /**
     * @return AdapterInterface[]
     */
    public function getAdapters();
}
