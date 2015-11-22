<?php

namespace ActiveCollab\Authentication\AuthenticatedUser;

/**
 * @package ActiveCollab\Authentication\AuthenticatedUser
 */
interface RepositoryInterface
{
    /**
     * @param  integer                         $user_id
     * @return AuthenticatedUserInterface|null
     */
    public function findById($user_id);

    /**
     * @param  string                          $username
     * @return AuthenticatedUserInterface|null
     */
    public function findByUsername($username);
}
