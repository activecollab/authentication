<?php

namespace ActiveCollab\Authentication\Session;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;

/**
 * @package ActiveCollab\Authentication\Token
 */
interface RepositoryInterface
{
    /**
     * Issue a new token
     *
     * @param  AuthenticatedUserInterface $user
     * @param  \DateTimeInterface|null    $expires_at
     * @return SessionInterface
     */
    public function createSession(AuthenticatedUserInterface $user, \DateTimeInterface $expires_at = null);

//    public function extendSession();
//
//    public function destroySession();
}
