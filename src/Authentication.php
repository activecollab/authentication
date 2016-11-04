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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use LogicException;

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

        return $adapter->authenticate($user);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapters()
    {
        return $this->adapters;
    }
}
