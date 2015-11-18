<?php

namespace ActiveCollab\Authentication\Adapter;

use Psr\Http\Message\RequestInterface;
use ActiveCollab\Authentication\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Exception\InvalidTokenException;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
class AuthorizationBearer implements AdapterInterface
{
    /**
     * Initialize authentication layer and see if we have a user who's already logged in
     *
     * @param  RequestInterface                $request
     * @return AuthenticatedUserInterface|null
     */
    public function initialize(RequestInterface $request)
    {
        $authorization = $request->getHeaderLine('Authorization');

        if (!empty($authorization) && substr($authorization, 0, 7) == 'Bearer ') {
            $token = trim(substr($authorization, 7));

            if (empty($token)) {
                throw new InvalidTokenException();
            }

            if ($token != 'my awesome token') {
                throw new InvalidTokenException();
            }
        }
    }

    /**
     * Authenticate with given credential agains authentication source
     *
     * @param  RequestInterface           $request
     * @return AuthenticatedUserInterface
     */
    public function authenticate(RequestInterface $request)
    {

    }
}