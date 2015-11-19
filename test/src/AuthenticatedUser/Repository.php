<?php

namespace ActiveCollab\Authentication\Test\AuthenticatedUser;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;

/**
 * @package ActiveCollab\Authentication\Test\AuthenticatedUser
 */
class Repository implements RepositoryInterface
{
    /**
     * @var array
     */
    private $users_by_username, $users_by_token, $users_by_session_id;

    /**
     * @param array $users_by_username
     * @param array $users_by_token
     * @param array $users_by_session_id
     */
    public function __construct(array $users_by_username = [], array $users_by_token = [], array $users_by_session_id = [])
    {
        $this->users_by_username = $users_by_username;
        $this->users_by_token = $users_by_token;
        $this->users_by_session_id = $users_by_session_id;
    }

    /**
     * @param  string                          $username
     * @return AuthenticatedUserInterface|null
     */
    public function findByUsername($username)
    {
        return isset($this->users_by_username[$username]) ? $this->users_by_username[$username] : null;
    }

    /**
     * Find a user by an authorization token
     *
     * @param  string                          $token
     * @return AuthenticatedUserInterface|null
     */
    public function findByToken($token)
    {
        return isset($this->users_by_token[$token]) ? $this->users_by_token[$token] : null;
    }

    /**
     * @var array
     */
    private $used_tokens = [];

    /**
     * @param string $token
     */
    public function recordTokenUsage($token)
    {
        if (empty($this->used_tokens[$token])) {
            $this->used_tokens[$token] = 0;
        }

        $this->used_tokens[$token]++;
    }

    /**
     * Return the number how many times was $token used
     *
     * @param  string $token
     * @return int
     */
    public function getTokenUsage($token)
    {
        return empty($this->used_tokens[$token]) ? 0 : $this->used_tokens[$token];
    }
}