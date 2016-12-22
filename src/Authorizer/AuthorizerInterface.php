<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;

/**
 * @package ActiveCollab\Authentication\Authorizer
 */
interface AuthorizerInterface
{
    const USERNAME_FORMAT_ANY = 'any';
    const USERNAME_FORMAT_ALPHANUM = 'alphanum';
    const USERNAME_FORMAT_EMAIL = 'email';

    /**
     * Perform user credentials verification against the real user database provider.
     *
     * @param  array                           $credentials
     * @return AuthenticatedUserInterface|null
     */
    public function verifyCredentials(array $credentials);
}
