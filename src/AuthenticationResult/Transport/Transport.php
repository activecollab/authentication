<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\AuthenticationResult\Transport;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\AuthenticationResult\Transport
 */
class Transport implements TransportInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var AuthenticatedUserInterface
     */
    private $authenticated_user;

    /**
     * @var AuthenticationResultInterface
     */
    private $authenticated_with;

    /**
     * @var array
     */
    private $additional_arguments;

    /**
     * Transport constructor.
     *
     * @param AdapterInterface                   $adapter
     * @param AuthenticatedUserInterface|null    $authenticated_user
     * @param AuthenticationResultInterface|null $authenticated_with
     * @param array                              $additional_arguments
     */
    public function __construct(AdapterInterface $adapter, AuthenticatedUserInterface $authenticated_user = null, AuthenticationResultInterface $authenticated_with = null, array $additional_arguments = [])
    {
        $this->adapter = $adapter;
        $this->authenticated_user = $authenticated_user;
        $this->authenticated_with = $authenticated_with;
        $this->additional_arguments = $additional_arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticatedUser()
    {
        return $this->authenticated_user;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticatedWith()
    {
        return $this->authenticated_with;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalArguments()
    {
        return $this->additional_arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function &addArgument($arg_name, $arg_value)
    {
        $this->additional_arguments[$arg_name] = $arg_value;

        return $this;
    }

    /**
     * Return response payload.
     *
     * @return mixed
     */
    public function getResponsePayload()
    {
        return isset($this->additional_arguments['_response_payload']) ? $this->additional_arguments['_response_payload'] : null;
    }

    /**
     * Set response payload.
     *
     * @param  mixed $payload
     * @return $this
     */
    public function &setResponsePayload($payload)
    {
        return $this->addArgument('_response_payload', $payload);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->authenticated_user) && empty($this->authenticated_with);
    }

    /**
     * @var bool
     */
    private $is_finalized = false;

    /**
     * Sign request and response based on authentication result.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @return array
     */
    public function finalize(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($this->isEmpty()) {
            throw new LogicException('Empty result cannot be used to finalize authentication');
        }

        if ($this->isFinalized()) {
            throw new LogicException('Authentication already finalized');
        }

        $result = $this->getAdapter()->finalize($request, $response, $this->getAuthenticatedUser(), $this->getAuthenticatedWith(), $this->getAdditionalArguments());
        $this->is_finalized = true;

        return $result;
    }

    /**
     * Return true if finalize method has been executed.
     *
     * @return bool
     */
    public function isFinalized()
    {
        return $this->is_finalized;
    }
}
