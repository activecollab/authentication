<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\AuthenticationResult\Transport;

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
     * @return ServerRequestInterface
     */
    public function getRequest();

    /**
     * @return ResponseInterface
     */
    public function getResponse();

    /**
     * @return AuthenticatedUserInterface
     */
    public function getAuthenticatedUser();

    /**
     * @return AuthenticationResultInterface
     */
    public function getAuthenticatedWith();

    /**
     * Return an array of any additional arguments that system whats to transport alongside the main four arguments.
     *
     * @return array
     */
    public function getAdditionalArguments();
}
