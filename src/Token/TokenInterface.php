<?php

namespace ActiveCollab\Authentication\Token;

use ActiveCollab\Authentication\AuthenticationResultInterface;


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
