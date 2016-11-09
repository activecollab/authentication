<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;

/**
 * @package ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication
 */
interface AuthenticationTransportInterface extends TransportInterface
{
    /**
     * @return AuthenticatedUserInterface|null
     */
    public function getAuthenticatedUser();

    /**
     * @return AuthenticationResultInterface|null
     */
    public function getAuthenticatedWith();
}
