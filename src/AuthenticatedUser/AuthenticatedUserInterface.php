<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\AuthenticatedUser;

use ActiveCollab\User\UserInterface;

/**
 * @package ActiveCollab\Authentication\AuthenticatedUser
 */
interface AuthenticatedUserInterface extends UserInterface
{
    /**
     * Return username that is used for authentication.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Check if $password is a valid password of this user.
     *
     * @param  string $password
     * @return bool
     */
    public function isValidPassword($password);

    /**
     * Return true if this user can authenticate.
     *
     * @return bool
     */
    public function canAuthenticate();
}
