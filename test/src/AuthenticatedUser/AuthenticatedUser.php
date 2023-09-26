<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\AuthenticatedUser;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\Username\UsernameInterface;
use ActiveCollab\User\UserInterface\ImplementationUsingFullName;

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

    private ?UsernameInterface $username = null;

    public function getUsername(): UsernameInterface
    {
        if (empty($this->username)) {
            $this->username = new class ($this->getEmail()) implements UsernameInterface {
                public function __construct(
                    private string $email,
                )
                {
                }

                public function __toString()
                {
                    return $this->getUsername();
                }

                public function getUsername(): string
                {
                    return $this->email;
                }
            };
        }

        return $this->username;
    }

    public function isValidPassword(string $password): bool
    {
        return $password === $this->password;
    }

    public function canAuthenticate(): bool
    {
        return $this->can_authenticate;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getFullName(),
            'email' => $this->getEmail(),
        ];
    }
}
