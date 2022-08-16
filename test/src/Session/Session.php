<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Session;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use DateTimeInterface;
use JsonSerializable;

/**
 * @package ActiveCollab\Authentication\Test\Session
 */
class Session implements SessionInterface
{
    const SESSION_TTL = 1800;
    const EXTENDED_SESSION_TTL = 1209600;

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
     * @var bool
     */
    public $is_extended_session = false;

    /**
     * @param string                 $session_id
     * @param string                 $user_id
     * @param DateTimeInterface|null $expires_at
     */
    public function __construct($session_id, $user_id, DateTimeInterface $expires_at = null)
    {
        $this->session_id = $session_id;
        $this->user_id = $user_id;
        $this->expires_at = $expires_at;
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticatedUser(UserRepositoryInterface $repository)
    {
        return $repository->findByUsername($this->user_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionTtl()
    {
        return $this->getIsExtendedSession() ? self::EXTENDED_SESSION_TTL : self::SESSION_TTL;
    }

    /**
     * Return value if is_extened_session field.
     *
     * @return bool
     */
    public function getIsExtendedSession()
    {
        return $this->is_extended_session;
    }

    /**
     * Set value of is_extended_value field.
     *
     * @param  bool  $value
     * @return $this
     */
    public function setIsExtendedSession($value)
    {
        $this->is_extended_session = (bool) $value;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $expires_at = null;

        if ($this->expires_at instanceof JsonSerializable) {
            $expires_at = $this->expires_at->jsonSerialize();
        } elseif ($this->expires_at instanceof DateTimeInterface) {
            $expires_at = $this->expires_at->getTimestamp();
        }

        return [
            'session_id' => $this->session_id,
            'user_id' => $this->user_id,
            'expires_at' => $expires_at,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function extendSession($reference_timestamp = null)
    {
    }
}
