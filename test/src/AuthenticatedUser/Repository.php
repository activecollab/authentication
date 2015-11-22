<?php

namespace ActiveCollab\Authentication\Test\AuthenticatedUser;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use LogicException;

/**
 * @package ActiveCollab\Authentication\Test\AuthenticatedUser
 */
class Repository implements RepositoryInterface
{
    /**
     * @var array
     */
    private $users_by_username;

    /**
     * @param array $users_by_username
     */
    public function __construct(array $users_by_username = [])
    {
        foreach ($users_by_username as $user) {
            if ($user instanceof AuthenticatedUserInterface) {
                $this->users_by_username[$user->getUsername()] = $user;
            } else {
                throw new LogicException('Users by username can only include users');
            }
        }
    }

    /**
     * @param  string                          $username
     * @return AuthenticatedUserInterface|null
     */
    public function findByUsername($username)
    {
        return isset($this->users_by_username[$username]) ? $this->users_by_username[$username] : null;
    }
}
