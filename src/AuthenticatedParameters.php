<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;

class AuthenticatedParameters
{
    /**
     * @var AuthenticatedUserInterface
     */
    public $authenticated_user;

    /**
     * @var AuthenticationResultInterface
     */
    public $authentication_result;

    /**
     * @var AdapterInterface
     */
    public $adapter;

    /**
     * @param AuthenticatedUserInterface    $authenticated_user
     * @param AuthenticationResultInterface $authentication_result
     * @param AdapterInterface              $adapter
     */
    public function __construct(AuthenticatedUserInterface $authenticated_user, AuthenticationResultInterface $authentication_result, AdapterInterface $adapter)
    {
        $this->authenticated_user = $authenticated_user;
        $this->authentication_result = $authentication_result;
        $this->adapter = $adapter;
    }
}
