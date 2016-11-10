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
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authorization\AuthorizationTransport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Transport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

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
     * @var callable|null
     */
    private $on_authencated_user_changed;

    /**
     * @param array $adapters
     */
    public function __construct(array $adapters)
    {
        foreach ($adapters as $adapter) {
            if (!($adapter instanceof AdapterInterface)) {
                throw new RuntimeException('Invalid object type provided');
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
            $this->setAuthenticatedUser($auth_result->getAuthenticatedUser());
            $this->setAuthenticatedWith($auth_result->getAuthenticatedWith());

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
        $user = $authorizer->verifyCredentials($credentials);
        $authenticated_with = $adapter->authenticate($user);

        return new AuthorizationTransport($adapter, $user, $authenticated_with, $payload);
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

        if (is_callable($this->on_authencated_user_changed)) {
            call_user_func($this->on_authencated_user_changed, $user);
        }

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
     * {@inheritdoc}
     */
    public function &setOnAuthenciatedUserChanged(callable $value = null)
    {
        $this->on_authencated_user_changed = $value;

        return $this;
    }
}
