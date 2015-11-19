<?php

namespace ActiveCollab\Authentication\AuthenticatedUser;

use ActiveCollab\User\UserInterface;

/**
 * @package ActiveCollab\Authentication
 */
interface AuthenticatedUserInterface extends UserInterface
{
    /**
     * Check if $password is a valid password of this user
     *
     * @param  string  $password
     * @return boolean
     */
    public function isValidPassword($password);

    /**
     * Return true if this user can authenticate
     *
     * @return boolean
     */
    public function canAuthenticate();
}
