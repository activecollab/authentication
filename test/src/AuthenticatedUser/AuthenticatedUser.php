<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\AuthenticatedUser;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\User\UserInterface\ImplementationUsingFullName;

/**
 * @package ActiveCollab\Authentication\Test\AuthenticatedUser
 */
class AuthenticatedUser implements AuthenticatedUserInterface
{
    use ImplementationUsingFullName;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $can_authenticate;

    /**
     * @param int    $id
     * @param string $email
     * @param string $name
     * @param string $password
     * @param bool   $can_authenticate
     */
    public function __construct($id, $email, $name, $password, $can_authenticate = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->can_authenticate = $can_authenticate;
    }

    /**
     * Return user ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullName(): ?string
    {
        return $this->name;
    }

    /**
     * Return user's password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function isValidPassword($password)
    {
        return $password === $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function canAuthenticate()
    {
        return $this->can_authenticate;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return ['id' => $this->getId(), 'name' => $this->getFullName(), 'email' => $this->getEmail()];
    }
}
