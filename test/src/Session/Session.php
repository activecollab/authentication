<?php

namespace ActiveCollab\Authentication\Test\Session;

use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Authentication\AuthenticationResultInterface\Implementation as AuthenticationResultInterfaceImplementation;

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
     * @var \DateTimeInterface
     */
    private $expires_at;

    /**
     * @param string                  $session_id
     * @param \DateTimeInterface|null $expires_at
     */
    public function __construct($session_id, \DateTimeInterface $expires_at = null)
    {
        $this->session_id = $session_id;
        $this->expires_at = $expires_at;
    }

    /**
     * @return array
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

        return ['session_id' => $this->session_id, 'expires_at' => $expires_at];
    }
}
