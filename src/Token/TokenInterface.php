<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Token;

use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;

interface TokenInterface extends AuthenticationResultInterface
{
    public function getTokenId(): string;
}
