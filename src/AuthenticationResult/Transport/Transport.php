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
class Transport implements TransportInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

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
     * @param ServerRequestInterface        $request
     * @param ResponseInterface             $response
     * @param AuthenticatedUserInterface    $authenticated_user
     * @param AuthenticationResultInterface $authenticated_with
     * @param array                         $additional_arguments
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response, AuthenticatedUserInterface $authenticated_user, AuthenticationResultInterface $authenticated_with, array $additional_arguments = [])
    {
        $this->request = $request;
        $this->response = $response;
        $this->authenticated_user = $authenticated_user;
        $this->authenticated_with = $authenticated_with;
        $this->additional_arguments = $additional_arguments;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return AuthenticatedUserInterface
     */
    public function getAuthenticatedUser()
    {
        return $this->authenticated_user;
    }

    /**
     * @return AuthenticationResultInterface
     */
    public function getAuthenticatedWith()
    {
        return $this->authenticated_with;
    }

    /**
     * Return an array of any additional arguments that system whats to transport alongside the main four arguments.
     *
     * @return array
     */
    public function getAdditionalArguments()
    {
        return $this->additional_arguments;
    }
}
