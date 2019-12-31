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
    private $is_applied_to_request = false;
    private $is_applied_to_response = false;

    public function applyToRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($this->isEmpty()) {
            throw new LogicException('Empty authentication transport cannot be applied');
        }

        if ($this->is_applied_to_request) {
            throw new LogicException('Authentication transport already applied');
        }

        return $request;
    }

    public function applyToResponse(ResponseInterface $response): ResponseInterface
    {
        if ($this->isEmpty()) {
            throw new LogicException('Empty authentication transport cannot be applied');
        }

        if ($this->is_applied_to_response) {
            throw new LogicException('Authentication transport already applied');
        }

        return $response;
    }

    public function applyTo(ServerRequestInterface $request, ResponseInterface $response): array
    {
        if ($this->isEmpty()) {
            throw new LogicException('Empty authentication transport cannot be applied');
        }

        if ($this->isApplied()) {
            throw new LogicException('Authentication transport already applied');
        }

        $result = $this->getAdapter()->applyTo($request, $response, $this);
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
