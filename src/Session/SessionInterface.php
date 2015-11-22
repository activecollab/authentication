<?php

namespace ActiveCollab\Authentication\Session;

use ActiveCollab\Authentication\AuthenticationResultInterface;

/**
 * @package ActiveCollab\Authentication\Session
 */
interface SessionInterface extends AuthenticationResultInterface
{
    /**
     * @return string
     */
    public function getSessionId();
}
