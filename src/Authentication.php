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

/**
 * @package ActiveCollab\Authentication
 */
class Authentication implements AuthenticationInterface
{
    /**
     * @var array
     */
    private $adapters;

    /**
     * Authenticated user instance.
     *
     * @var AuthenticatedUserInterface
     */
    private $authenticated_user;

    /**
     * @var AuthenticationResultInterface
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

    /**
     * @param array $adapters
     */
    public function __construct(array $adapters)
    {
        foreach ($adapters as $adapter) {
            if (!($adapter instanceof AdapterInterface)) {
                throw new LogicException('Invalid authentication adapter provided');
            }
        }

        $this->adapters = $adapters;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $auth_result = $this->authenticatedUsingAdapters($request);

        if ($auth_result instanceof TransportInterface && !$auth_result->isEmpty()) {
            if ($auth_result instanceof AuthenticationTransportInterface) {
                $this->setAuthenticatedUser($auth_result->getAuthenticatedUser());
                $this->setAuthenticatedWith($auth_result->getAuthenticatedWith());

                $this->triggerEvent('user_authenticated', $auth_result->getAuthenticatedUser(), $auth_result->getAuthenticatedWith());
            }

            list($request, $response) = $auth_result->applyTo($request, $response);
        }

        if ($next) {
            $response = $next($request, $response);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function authorize(AuthorizerInterface $authorizer, AdapterInterface $adapter, array $credentials, $payload = null)
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

    /**
     * {@inheritdoc}
     */
    public function terminate(AdapterInterface $adapter, AuthenticationResultInterface $authenticated_with)
    {
        $termination_result = $adapter->terminate($authenticated_with);

        $this->triggerEvent('user_deauthenticated', $authenticated_with);

        return $termination_result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapters()
    {
        return $this->adapters;
    }

    /**
     * @param  ServerRequestInterface  $request
     * @return TransportInterface|null
     * @throws Exception
     */
    private function authenticatedUsingAdapters(ServerRequestInterface $request)
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

    /**
     * {@inheritdoc}
     */
    public function &getAuthenticatedUser()
    {
        return $this->authenticated_user;
    }

    /**
     * {@inheritdoc}
     */
    public function &setAuthenticatedUser(AuthenticatedUserInterface $user = null)
    {
        $this->authenticated_user = $user;

        $this->triggerEvent('user_set', $user);

        return $this;
    }

    /**
     * @return AuthenticationResultInterface|null
     */
    public function getAuthenticatedWith()
    {
        return $this->authenticated_with;
    }

    /**
     * {@inheritdoc}
     */
    public function &setAuthenticatedWith(AuthenticationResultInterface $value)
    {
        $this->authenticated_with = $value;

        return $this;
    }

    /**
     * Trigger an internal event.
     *
     * @param  string $event_name
     * @param  array  $arguments
     * @return $this
     */
    private function &triggerEvent($event_name, ...$arguments)
    {
        $property_name = "on_{$event_name}";

        /** @var callable $handler */
        foreach ($this->$property_name as $handler) {
            call_user_func_array($handler, $arguments);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function &onUserAuthenticated(callable $value)
    {
        $this->on_user_authenticated[] = $value;

        return $this;
    }

    public function &onUserAuthorized(callable $value)
    {
        $this->on_user_authorized[] = $value;

        return $this;
    }

    public function &onUserAuthorizationFailed(callable $value)
    {
        $this->on_user_authorization_failed[] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function &onUserSet(callable $value)
    {
        $this->on_user_set[] = $value;

        return $this;
    }

    public function &onUserDeauthenticated(callable $value)
    {
        $this->on_user_deauthenticated[] = $value;

        return $this;
    }

    /**
     * Kept for backward compatibility reasons. Will be removed.
     *
     * {@inheritdoc}
     */
    public function &setOnAuthenciatedUserChanged(callable $value = null)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Value needs to be a callable.');
        }

        return $this->onUserSet($value);
    }
}
