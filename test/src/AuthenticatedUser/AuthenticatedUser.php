<?php

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
    private $name, $email;

    /**
     * @param integer $id
     * @param string  $email
     * @param string  $name
     */
    public function __construct($id, $email, $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * Return user ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return email address of a given user
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Return first name of this user
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getFullName(),
            'email' => $this->getEmail(),
        ];
    }
}