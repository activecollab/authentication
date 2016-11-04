<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\AuthenticationResult\Transport;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;

/**
 * @package ActiveCollab\Authentication\AuthenticationResult\Transport
 */
class Transport implements TransportInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var AuthenticatedUserInterface
     */
    private $authenticated_user;

    /**
     * @var AuthenticationResultInterface
     */
    private $authenticated_with;

    /**
     * @var array
     */
    private $additional_arguments;

    /**
     * Transport constructor.
     *
     * @param AdapterInterface                   $adapter
     * @param AuthenticatedUserInterface|null    $authenticated_user
     * @param AuthenticationResultInterface|null $authenticated_with
     * @param array                              $additional_arguments
     */
    public function __construct(AdapterInterface $adapter, AuthenticatedUserInterface $authenticated_user = null, AuthenticationResultInterface $authenticated_with = null, array $additional_arguments = [])
    {
        $this->adapter = $adapter;
        $this->authenticated_user = $authenticated_user;
        $this->authenticated_with = $authenticated_with;
        $this->additional_arguments = $additional_arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticatedUser()
    {
        return $this->authenticated_user;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticatedWith()
    {
        return $this->authenticated_with;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalArguments()
    {
        return $this->additional_arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function &addArgument($arg_name, $arg_value)
    {
        $this->additional_arguments[$arg_name] = $arg_value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->authenticated_user) && empty($this->authenticated_with);
    }
}
