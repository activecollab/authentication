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

    /**
     * Find a user by an authorization token
     *
     * @param  string                          $token
     * @return AuthenticatedUserInterface|null
     */
    public function findByToken($token);

    /**
     * @param string $token
     */
    public function recordTokenUsage($token);
}
