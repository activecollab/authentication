<?php

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
interface AdapterInterface
{
    /**
     * Initialize authentication layer and see if we have a user who's already logged in
     *
     * @param  ServerRequestInterface     $request
     * @param  null                       $authenticated_with
     * @return AuthenticatedUserInterface
     */
    public function initialize(ServerRequestInterface $request, &$authenticated_with = null);

    /**
     * Authenticate with given credential agains authentication source
     *
     * @param  ServerRequestInterface        $request
     * @return AuthenticationResultInterface
     */
    public function authenticate(ServerRequestInterface $request);

    /**
     * Terminate an instance that was used to authenticate a user
     *
     * @param AuthenticationResultInterface $authenticated_with
     */
    public function terminate(AuthenticationResultInterface $authenticated_with);
}
