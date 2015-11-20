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
     * @param  string                          $session_id
     * @return AuthenticatedUserInterface|null
     */
    public function findBySessionId($session_id)
    {
        return isset($this->users_by_session_id[$session_id]) ? $this->users_by_session_id[$session_id] : null;
    }

    /**
     * @var array
     */
    private $used_session = [];

    /**
     * @param string $session_id
     */
    public function recordSessionUsage($session_id)
    {
        if (empty($this->used_session[$session_id])) {
            $this->used_session[$session_id] = 0;
        }

        $this->used_session[$session_id]++;
    }

    /**
     * Return the number how many times was a session used
     *
     * @param  string $session_id
     * @return int
     */
    public function getSessionUsage($session_id)
    {
        return empty($this->used_session[$session_id]) ? 0 : $this->used_session[$session_id];
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