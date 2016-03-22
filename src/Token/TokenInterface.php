<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Token;

use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;

/**
 * @package ActiveCollab\Authentication\Token
 */
interface TokenInterface extends AuthenticationResultInterface
{
    /**
     * @return string
     */
    public function getTokenId();
}
