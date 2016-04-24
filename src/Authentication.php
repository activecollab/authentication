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
     * @param array                    $adapters
     * @param AuthorizerInterface|null $authorizer
     */
    public function __construct(array $adapters, AuthorizerInterface $authorizer = null)
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
    public function setAuthorizer(AuthorizerInterface $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(RequestInterface $request)
    {
        $exception = null;
        $results = ['authenticated_parameters' => []];

        foreach ($this->adapters as $adapter) {
            try {
                $result = $adapter->initialize($request);
                if ($result instanceof AuthenticatedParameters) {
                    $results['authenticated_parameters'][] = $result;
                }
            } catch (Exception $e) {
                $exception = $e;
            }
        }

        if (empty($results['authenticated_parameters']) && $exception) {
            throw $exception;
        }

        if (count($results['authenticated_parameters']) > 1) {
            throw new InvalidAuthenticationRequestException('You can not be authenticated with more than one authentication method');
        }

        return $request->withAttribute('authenticated_parameters', $results['authenticated_parameters'][0]);
    }

    /**
     * {@inheritdoc}
     */
    public function authorize(RequestInterface $request, array $credentials = [])
    {
        if (!$this->authorizer) {
            throw new RuntimeException('Authorizer object is not configured');
        }

        if (!$this->authorizer->verifyCredentials($credentials)) {
            throw new InvalidCredentialsException();
        }

        $authenticated_parameters = $request->getAttribute('authenticated_parameters');

        return $authenticated_parameters->adapter->authenticate($authenticated_parameters->authenticated_user);
    }
}
