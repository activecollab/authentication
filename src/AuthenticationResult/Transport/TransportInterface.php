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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\AuthenticationResult\Transport
 */
interface TransportInterface
{
    /**
     * Return adapter which produced this initialization result.
     *
     * @return AdapterInterface
     */
    public function getAdapter();

    /**
     * @return AuthenticatedUserInterface|null
     */
    public function getAuthenticatedUser();

    /**
     * @return AuthenticationResultInterface|null
     */
    public function getAuthenticatedWith();

    /**
     * Return a possible response payload after successful authorization.
     *
     * @return mixed
     */
    public function getPayload();

    /**
     * Set authorization response payload, if neededs.
     *
     * @param  mixed $value
     * @return $this
     */
    public function &setPayload($value);

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * Apply authentication result to request and response, and return modified objects.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @return array
     */
    public function applyTo(ServerRequestInterface $request, ResponseInterface $response);

    /**
     * Return true if finalize method has been executed.
     *
     * @return bool
     */
    public function isFinalized();
}
