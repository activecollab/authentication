<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter\InitializationResult;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;

/**
 * @package ActiveCollab\Authentication\AuthenticationResult\Transport
 */
class InitializationResult implements InitializationResultInterface
{
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
     * @param AuthenticatedUserInterface    $authenticated_user
     * @param AuthenticationResultInterface $authenticated_with
     * @param array                         $additional_arguments
     */
    public function __construct(AuthenticatedUserInterface $authenticated_user, AuthenticationResultInterface $authenticated_with, array $additional_arguments = [])
    {
        $this->authenticated_user = $authenticated_user;
        $this->authenticated_with = $authenticated_with;
        $this->additional_arguments = $additional_arguments;
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
