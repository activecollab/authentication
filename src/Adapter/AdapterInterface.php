<?php

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
interface AdapterInterface
{
    /**
     * Initialize authentication layer and see if we have a user who's already logged in
     *
     * @param  RequestInterface           $request
     * @return AuthenticatedUserInterface
     */
    public function initialize(RequestInterface $request);

    /**
     * Authenticate with given credential agains authentication source
     *
     * @param  RequestInterface              $request
     * @return AuthenticationResultInterface
     */
    public function authenticate(RequestInterface $request);
}
