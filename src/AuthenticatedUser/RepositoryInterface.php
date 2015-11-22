<?php

namespace ActiveCollab\Authentication\AuthenticatedUser;

/**
 * @package ActiveCollab\Authentication\AuthenticatedUser
 */
interface RepositoryInterface
{
    /**
     * @param  string                          $username
     * @return AuthenticatedUserInterface|null
     */
    public function findByUsername($username);
}
