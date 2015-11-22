<?php

namespace ActiveCollab\Authentication\Test\Session;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\AuthenticationResultInterface\Implementation as AuthenticationResultInterfaceImplementation;
use ActiveCollab\Authentication\Session\SessionInterface;

/**
 * @package ActiveCollab\Authentication\Test\Session
 */
class Session implements SessionInterface
{
    use AuthenticationResultInterfaceImplementation;

    /**
     * @var string
     */
    private $session_id;

    /**
     * @var string
     */
    private $user_id;

    /**
     * @var \DateTimeInterface
     */
    private $expires_at;

    /**
     * @param string                  $session_id
     * @param string                  $user_id
     * @param \DateTimeInterface|null $expires_at
     */
    public function __construct($session_id, $user_id, \DateTimeInterface $expires_at = null)
    {
        $this->session_id = $session_id;
        $this->user_id = $user_id;
        $this->expires_at = $expires_at;
    }

    /**
     * {@inheritdocs}
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /**
     * {@inheritdocs}
     */
    public function getAuthenticatedUser(UserRepositoryInterface $repository)
    {
        return $repository->findBySessionId($this->session_id);
    }

    /**
     * {@inheritdocs}
     */
    public function jsonSerialize()
    {
        if ($this->expires_at instanceof \JsonSerializable) {
            $expires_at = $this->expires_at->jsonSerialize();
        } elseif ($this->expires_at instanceof \DateTimeInterface) {
            $expires_at = $this->expires_at->getTimestamp();
        } else {
            $expires_at = null;
        }

        return ['session_id' => $this->session_id, 'user_id' => $this->user_id, 'expires_at' => $expires_at];
    }
}
