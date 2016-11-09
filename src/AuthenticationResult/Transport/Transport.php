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
     * @var mixed
     */
    private $payload;

    /**
     * Transport constructor.
     *
     * @param AdapterInterface                   $adapter
     * @param AuthenticatedUserInterface|null    $authenticated_user
     * @param AuthenticationResultInterface|null $authenticated_with
     * @param mixed                              $payload
     */
    public function __construct(AdapterInterface $adapter, AuthenticatedUserInterface $authenticated_user = null, AuthenticationResultInterface $authenticated_with = null, $payload = null)
    {
        $this->adapter = $adapter;
        $this->authenticated_user = $authenticated_user;
        $this->authenticated_with = $authenticated_with;
        $this->payload = $payload;
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
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * {@inheritdoc}
     */
    public function &setPayload($value)
    {
        $this->payload = $value;

        return $this;
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
    public function applyTo(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($this->isEmpty()) {
            throw new LogicException('Empty result cannot be used to finalize authentication');
        }

        if ($this->isFinalized()) {
            throw new LogicException('Authentication already finalized');
        }

        $result = $this->getAdapter()->finalize($request, $response, $this->getAuthenticatedUser(), $this->getAuthenticatedWith(), $this->getPayload());
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
