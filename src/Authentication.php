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
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransportInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authorization\AuthorizationTransport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Transport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;
use Exception;
use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authentication implements AuthenticationInterface
{
    /**
     * @var AdapterInterface[]
     */
    private $adapters;

    /**
     * Authenticated user instance.
     *
     * @var AuthenticatedUserInterface|null
     */
    private $authenticated_user;

    /**
     * @var AuthenticationResultInterface|null
     */
    private $authenticated_with;

    /**
     * @var callable[]
     */
    private $on_user_authenticated = [];

    /**
     * @var callable[]
     */
    private $on_user_authorized = [];

    /**
     * @var callable[]
     */
    private $on_user_authorization_failed = [];

    /**
     * @var callable[]
     */
    private $on_user_set = [];

    /**
     * @var callable[]
     */
    private $on_user_deauthenticated = [];

    public function __construct(AdapterInterface ...$adapters)
    {
        foreach ($adapters as $adapter) {
            if (!($adapter instanceof AdapterInterface)) {
                throw new LogicException('Invalid authentication adapter provided');
            }
        }

        $this->adapters = $adapters;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ): ResponseInterface
    {
        $auth_result = $this->authenticatedUsingAdapters($request);

        if ($auth_result instanceof TransportInterface && !$auth_result->isEmpty()) {
            if ($auth_result instanceof AuthenticationTransportInterface) {
                $this->setAuthenticatedUser($auth_result->getAuthenticatedUser());
                $this->setAuthenticatedWith($auth_result->getAuthenticatedWith());

                $this->triggerEvent(
                    'user_authenticated',
                    $auth_result->getAuthenticatedUser(),
                    $auth_result->getAuthenticatedWith()
                );
            }

            [
                $request,
                $response,
            ] = $auth_result->applyTo($request, $response);
        }

        if ($next) {
            $response = $next($request, $response);
        }

        return $response;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        $auth_result = $this->authenticatedUsingAdapters($request);

        if ($auth_result instanceof TransportInterface 
            && !$auth_result->isEmpty()
        ) {
            if ($auth_result instanceof AuthenticationTransportInterface) {
                $this->setAuthenticatedUser($auth_result->getAuthenticatedUser());
                $this->setAuthenticatedWith($auth_result->getAuthenticatedWith());

                $this->triggerEvent(
                    'user_authenticated',
                    $auth_result->getAuthenticatedUser(),
                    $auth_result->getAuthenticatedWith()
                );
            }

            $request = $auth_result->applyToRequest($request);
        }

        $response = $handler->handle($request);

        if ($auth_result instanceof TransportInterface
            && !$auth_result->isEmpty()
        ) {
            $response = $auth_result->applyToResponse($response);
        }

        return $response;
    }

    public function authorize(
        AuthorizerInterface $authorizer,
        AdapterInterface $adapter,
        array $credentials,
        $payload = null
    ): TransportInterface
    {
        try {
            $user = $authorizer->verifyCredentials($credentials);
            $authenticated_with = $adapter->authenticate($user, $credentials);

            $this->triggerEvent('user_authorized', $user, $authenticated_with);

            return new AuthorizationTransport($adapter, $user, $authenticated_with, $payload);
        } catch (Exception $e) {
            $this->triggerEvent('user_authorization_failed', $credentials, $e);

            throw $e;
        }
    }

    public function terminate(
        AdapterInterface $adapter,
        AuthenticationResultInterface $authenticated_with
    ): TransportInterface
    {
        $termination_result = $adapter->terminate($authenticated_with);

        $this->triggerEvent('user_deauthenticated', $authenticated_with);

        return $termination_result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapters(): iterable
    {
        return $this->adapters;
    }

    private function authenticatedUsingAdapters(ServerRequestInterface $request): ?TransportInterface
    {
        $last_exception = null;
        $results = [];

        /** @var AdapterInterface $adapter */
        foreach ($this->adapters as $adapter) {
            try {
                $initialization_result = $adapter->initialize($request);

                if ($initialization_result instanceof Transport && !$initialization_result->isEmpty()) {
                    $results[] = $initialization_result;
                }
            } catch (Exception $e) {
                $last_exception = $e;
            }
        }

        if (empty($results)) {
            if ($last_exception) {
                throw $last_exception;
            }

            return null;
        }

        if (count($results) > 1) {
            throw new InvalidAuthenticationRequestException('You can not be authenticated with more than one authentication method');
        }

        return $results[0];
    }

    public function getAuthenticatedUser(): ?AuthenticatedUserInterface
    {
        return $this->authenticated_user;
    }

    public function setAuthenticatedUser(AuthenticatedUserInterface $user = null): AuthenticationInterface
    {
        $this->authenticated_user = $user;

        $this->triggerEvent('user_set', $user);

        return $this;
    }

    public function getAuthenticatedWith(): ?AuthenticationResultInterface
    {
        return $this->authenticated_with;
    }

    public function setAuthenticatedWith(AuthenticationResultInterface $value): AuthenticationInterface
    {
        $this->authenticated_with = $value;

        return $this;
    }

    private function triggerEvent(string $event_name, ...$arguments): AuthenticationInterface
    {
        $property_name = "on_{$event_name}";

        /** @var callable $handler */
        foreach ($this->$property_name as $handler) {
            call_user_func_array($handler, $arguments);
        }

        return $this;
    }

    public function onUserAuthenticated(callable $value): AuthenticationInterface
    {
        $this->on_user_authenticated[] = $value;

        return $this;
    }

    public function onUserAuthorized(callable $value): AuthenticationInterface
    {
        $this->on_user_authorized[] = $value;

        return $this;
    }

    public function onUserAuthorizationFailed(callable $value): AuthenticationInterface
    {
        $this->on_user_authorization_failed[] = $value;

        return $this;
    }

    public function onUserSet(callable $value): AuthenticationInterface
    {
        $this->on_user_set[] = $value;

        return $this;
    }

    public function onUserDeauthenticated(callable $value): AuthenticationInterface
    {
        $this->on_user_deauthenticated[] = $value;

        return $this;
    }

    /**
     * Kept for backward compatibility reasons. Will be removed.
     *
     * {@inheritdoc}
     */
    public function setOnAuthenciatedUserChanged(callable $value = null): AuthenticationInterface
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Value needs to be a callable.');
        }

        return $this->onUserSet($value);
    }
}
