<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;
use ActiveCollab\Authentication\Exception\InvalidCredentialsException;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

class Authentication implements AuthenticationInterface
{
    /**
     * @var array
     */
    private $adapters;

    /**
     * @var AuthorizerInterface
     */
    private $authorizer;

    /**
     * @param array               $adapters
     * @param AuthorizerInterface $authorizer
     */
    public function __construct(array $adapters, AuthorizerInterface $authorizer)
    {
        foreach ($adapters as $adapter) {
            if (!($adapter instanceof AdapterInterface)) {
                throw new RuntimeException('Invalid object type provided');
            }
        }

        $this->adapters = $adapters;
        $this->authorizer = $authorizer;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(RequestInterface $request)
    {
        $exception = null;
        $results = ['adapter' => [], 'result' => []];

        foreach ($this->adapters as $adapter) {
            try {
                $result = $adapter->initialize($request);
                if ($result instanceof AuthenticatedUserInterface) {
                    $results['adapter'][] = $adapter;
                    $results['result'][] = $result;
                }
            } catch (Exception $e) {
                $exception = $e;
            }
        }

        if (empty($results['adapter']) && $exception) {
            throw $exception;
        }

        if (count($results['adapter']) > 1) {
            throw new InvalidAuthenticationRequestException('You can not be authenticated with more than one authentication method');
        }

        return $request
            ->withAttribute('authentication_adapter', $results['adapter'][0])
            ->withAttribute('authenticated_user', $results['result'][0]);
    }

    /**
     * {@inheritdoc}
     */
    public function authorize(RequestInterface $request, array $credentials = [])
    {
        if (!$this->authorizer->verifyCredentials($credentials)) {
            throw new InvalidCredentialsException();
        }

        $adapter = $request->getAttribute('authentication_adapter');

        return $adapter->authenticate($request->getAttribute('authenticated_user'));
    }
}
