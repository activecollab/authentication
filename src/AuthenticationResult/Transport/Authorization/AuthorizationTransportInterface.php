<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\AuthenticationResult\Transport\Authorization;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;

interface AuthorizationTransportInterface extends TransportInterface
{
    /**
     * @return AuthenticatedUserInterface
     */
    public function getAuthenticatedUser();

    /**
     * @return AuthenticationResultInterface
     */
    public function getAuthenticatedWith();
}
