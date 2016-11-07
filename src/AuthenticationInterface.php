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
     * @param  AuthorizerInterface $authorizer
     * @param  AdapterInterface    $adapter
     * @param  array               $credentials
     * @return TransportInterface
     */
    public function authorize(AuthorizerInterface $authorizer, AdapterInterface $adapter, array $credentials);

    /**
     * @return AdapterInterface[]
     */
    public function getAdapters();

    /**
     * Return name of request attribute where execution result is stored.
     *
     * @return string
     */
    public function getExecutionResultAttributeName();

    /**
     * Set name of request attribute where execution result is stored.
     *
     * @param  string $value
     * @return $this
     */
    public function &setExecutionResultAttributeName($value);

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
     * Return authenticated in user.
     *
     * @return AuthenticatedUserInterface
     */
    public function &getAuthenticatedUser();

    /**
     * Override authentication adapter and force set logged user for this request.
     *
     * @param  AuthenticatedUserInterface|null            $user
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
    protected function &setAuthenticatedWith(AuthenticationResultInterface $value);

    /**
     * @param callable|null $value
     * @return $this
     */
    public function &setOnAuthenciatedUserChanged(callable $value = null);
}
