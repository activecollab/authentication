<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\AuthenticationResult\Transport;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\AuthenticationResult\Transport
 */
abstract class Transport implements TransportInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var mixed
     */
    private $payload;

    /**
     * Transport constructor.
     *
     * @param AdapterInterface $adapter
     * @param mixed            $payload
     */
    public function __construct(AdapterInterface $adapter, $payload = null)
    {
        $this->adapter = $adapter;
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
        return false;
    }

    /**
     * @var bool
     */
    private $is_applied = false;

    /**
     * {@inheritdoc}
     */
    public function applyTo(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($this->isEmpty()) {
            throw new LogicException('Empty result cannot be used to finalize authentication');
        }

        if ($this->isApplied()) {
            throw new LogicException('Authentication already finalized');
        }

        $result = $this->getAdapter()->finalize($request, $response, $this->getAuthenticatedUser(), $this->getAuthenticatedWith(), $this->getPayload());
        $this->is_applied = true;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplied()
    {
        return $this->is_applied;
    }
}
