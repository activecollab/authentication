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
use InvalidArgumentException;
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
    private $request_key_for_encapsulated_result = 'encapsulated_authentication_result';

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
     * @return string
     */
    public function getRequestKeyForEncapsulatedResult()
    {
        return $this->request_key_for_encapsulated_result;
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function &setRequestKeyForEncapsulatedResult($value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Value cannot be empty');
        }

        $this->request_key_for_encapsulated_result = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ServerRequestInterface $request)
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
    public function finalize(ServerRequestInterface $request, ResponseInterface $response, TransportInterface $authentication_result)
    {
        if ($authentication_result->isEmpty()) {
            throw new LogicException('Finalization is not possible with an empty authentication result');
        }

        return $authentication_result->getAdapter()->finalize($request, $response, $authentication_result->getAuthenticatedUser(), $authentication_result->getAuthenticatedWith(), $authentication_result->getAdditionalArguments());
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
     * Authentication can be used as a PSR-7 middleware.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @param  callable|null          $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $auth_result = $this->runAdapters($request);

        if ($auth_result instanceof TransportInterface && !$auth_result->isEmpty()) {
            list($request, $response) = $auth_result->finalize($request, $response);
        }

        // Initialize adapters and set the result as request key
        $request = $request->withAttribute($this->getRequestKeyForEncapsulatedResult(), $auth_result);

        if ($next) {
            $response = $next($request, $response);
        }

        return $response;
    }

    /**
     * @param  ServerRequestInterface  $request
     * @return TransportInterface|null
     * @throws Exception
     */
    private function runAdapters(ServerRequestInterface $request)
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
