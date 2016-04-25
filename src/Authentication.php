<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;
use Exception;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

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
    public function initialize(RequestInterface $request)
    {
        $exception = null;
        $results = ['authenticated_user' => [], 'authenticated_with' => []];

        foreach ($this->adapters as $adapter) {
            try {
                $result = $adapter->initialize($request);
                if ($result) {
                    $results['authenticated_user'][] = $result['authenticated_user'];
                    $results['authenticated_with'][] = $result['authenticated_with'];
                }
            } catch (Exception $e) {
                $exception = $e;
            }
        }

        if (empty($results['authenticated_user'])) {
            if ($exception) {
                throw $exception;
            }

            return $request;
        }

        if (count($results['authenticated_user']) > 1) {
            throw new InvalidAuthenticationRequestException('You can not be authenticated with more than one authentication method');
        }

        return $request
            ->withAttribute('authenticated_user', $results['authenticated_user'][0])
            ->withAttribute('authenticated_with', $results['authenticated_with'][0]);
    }

    /**
     * {@inheritdoc}
     */
    public function authorize(AuthorizerInterface $authorizer, AdapterInterface $adapter, array $credentials)
    {
        $user = $authorizer->verifyCredentials($credentials);

        return $adapter->authenticate($user);
    }
}
