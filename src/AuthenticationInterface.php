<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

interface AuthenticationInterface extends MiddlewareInterface
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ): ResponseInterface;

    public function authorize(
        AuthorizerInterface $authorizer,
        AdapterInterface $adapter,
        array $credentials,
        $payload = null
    ): TransportInterface;

    public function terminate(
        AdapterInterface $adapter,
        AuthenticationResultInterface $authenticatedWith
    ): TransportInterface;

    /**
     * @return AdapterInterface[]|iterable
     */
    public function getAdapters(): iterable;

    public function getAuthenticatedUser(): ?AuthenticatedUserInterface;
    public function setAuthenticatedUser(AuthenticatedUserInterface $user = null): AuthenticationInterface;

    public function getAuthenticatedWith(): ?AuthenticationResultInterface;
    public function setAuthenticatedWith(?AuthenticationResultInterface $value): AuthenticationInterface;

    public function onUserAuthenticated(callable $value): AuthenticationInterface;
    public function onUserAuthorized(callable $value): AuthenticationInterface;
    public function onUserAuthorizationFailed(callable $value): AuthenticationInterface;
    public function onUserSet(callable $value): AuthenticationInterface;
    public function onUserDeauthenticated(callable $value): AuthenticationInterface;

    /**
     * Use onUserSet() instead.
     *
     * @param  callable|null $value
     * @return $this
     * @deprecated
     */
    public function setOnAuthenciatedUserChanged(callable $value = null): AuthenticationInterface;
}
