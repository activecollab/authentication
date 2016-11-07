<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Transport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;
use Exception;
use LogicException;
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
     * @var string
     */
    private $execution_result_attribute_name;

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
    public function initialize(ServerRequestInterface $request)
    {
        // @TODO Legacy method

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
    public function finalize(ServerRequestInterface $request, ResponseInterface $response, TransportInterface $authentication_result)
    {
        // @TODO Legacy method

        if ($authentication_result->isEmpty()) {
            throw new LogicException('Finalization is not possible with an empty authentication result');
        }

        return $authentication_result->getAdapter()->finalize($request, $response, $authentication_result->getAuthenticatedUser(), $authentication_result->getAuthenticatedWith(), $authentication_result->getPayload());
    }

    /**
     * {@inheritdoc}
     */
    public function authorize(AuthorizerInterface $authorizer, AdapterInterface $adapter, array $credentials)
    {
        $user = $authorizer->verifyCredentials($credentials);
        $authenticated_with = $adapter->authenticate($user);

        return new Transport($adapter, $user, $authenticated_with);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapters()
    {
        return $this->adapters;
    }

    /**
     * Return name of request attribute where execution result is stored.
     *
     * @return string
     */
    public function getExecutionResultAttributeName()
    {
        return $this->execution_result_attribute_name;
    }

    /**
     * Set name of request attribute where execution result is stored.
     *
     * @param  string $value
     * @return $this
     */
    public function &setExecutionResultAttributeName($value)
    {
        $this->execution_result_attribute_name = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $auth_result = $this->initializeAdapters($request);

        if ($auth_result instanceof TransportInterface && !$auth_result->isEmpty()) {
            list($request, $response) = $auth_result->finalize($request, $response);
        }

        if ($next) {
            $response = $next($request, $response);
        }

        // In case we find an execution result that is encapsulated authentication result, finalize it.
        // This situation happens when log in action is called for example, and result fo authorisation is returned and
        // stored as a request attribute.
        if ($this->getExecutionResultAttributeName()) {
            $exec_result = $request->getAttribute($this->getExecutionResultAttributeName());

            if ($exec_result instanceof TransportInterface && !$exec_result->isEmpty() && !$exec_result->isFinalized()) {
                $response = $exec_result->finalize($request, $response)[1];
            }
        }

        return $response;
    }

    /**
     * @param  ServerRequestInterface  $request
     * @return TransportInterface|null
     * @throws Exception
     */
    private function initializeAdapters(ServerRequestInterface $request)
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
}
