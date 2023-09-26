<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\AuthenticatedUser;

use ActiveCollab\Authentication\AuthenticatedUser\Username\UsernameInterface;
use ActiveCollab\User\UserInterface;

interface AuthenticatedUserInterface extends UserInterface
{
    /**
     * Return username that is used for authentication (email, unique username, phone number...).
     */
    public function getUsername(): UsernameInterface;

    /**
     * Check if $password is a valid password of this user.
     */
    public function isValidPassword(string $password): bool;

    /**
     * Return true if this user can authenticate.
     */
    public function canAuthenticate(): bool;
}
