<?php

namespace ActiveCollab\Authentication\Session;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;

/**
 * @package ActiveCollab\Authentication\Session
 */
interface SessionInterface extends AuthenticationResultInterface
{
    /**
     * @return string
     */
    public function getSessionId();

    /**
     * Get authenticated user from the repository
     *
     * @param  UserRepositoryInterface    $repository
     * @return AuthenticatedUserInterface
     */
    public function getAuthenticatedUser(UserRepositoryInterface $repository);
}
