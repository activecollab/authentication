<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication
 */
interface AuthenticationInterface
{
    /**
     * Authentication can be used as a PSR-7 middleware.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @param  callable|null          $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null);

    /**
     * Authorize and authenticate with given credentials against authorization/authentication source.
     *
     * @param  AuthorizerInterface $authorizer
     * @param  AdapterInterface    $adapter
     * @param  array               $credentials
     * @param  mixed               $payload
     * @return TransportInterface
     */
    public function authorize(AuthorizerInterface $authorizer, AdapterInterface $adapter, array $credentials, $payload = null);

    /**
     * Deauthetnicate.
     *
     * @param  AdapterInterface              $adapter
     * @param  AuthenticationResultInterface $authenticated_with
     * @return TransportInterface
     */
    public function terminate(AdapterInterface $adapter, AuthenticationResultInterface $authenticated_with);

    /**
     * @return AdapterInterface[]
     */
    public function getAdapters();

    /**
     * Return authenticated in user.
     *
     * @return AuthenticatedUserInterface
     */
    public function &getAuthenticatedUser();

    /**
     * Override authentication adapter and force set logged user for this request.
     *
     * @param  AuthenticatedUserInterface|null $user
     * @return $this
     */
    public function &setAuthenticatedUser(AuthenticatedUserInterface $user = null);

    /**
     * @return AuthenticationResultInterface|null
     */
    public function getAuthenticatedWith();

    /**
     * @param  AuthenticationResultInterface $value
     * @return $this
     */
    public function &setAuthenticatedWith(AuthenticationResultInterface $value);

    /**
     * @param  callable $value
     * @return $this
     */
    public function &onUserAuthenticated(callable $value);

    /**
     * @param  callable $value
     * @return $this
     */
    public function &onUserAuthorized(callable $value);

    /**
     * @param  callable $value
     * @return $this
     */
    public function &onUserAuthorizationFailed(callable $value);

    /**
     * @param  callable $value
     * @return $this
     */
    public function &onUserSet(callable $value);

    /**
     * @param  callable $value
     * @return $this
     */
    public function &onUserDeauthenticated(callable $value);

    /**
     * Use onUserSet() instead.
     *
     * @param  callable|null $value
     * @return $this
     * @deprecated
     */
    public function &setOnAuthenciatedUserChanged(callable $value = null);
}
