<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\AuthenticationResult\Transport\Authorization;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Transport;

class AuthorizationTransport extends Transport implements AuthorizationTransportInterface
{
    /**
     * @var AuthenticatedUserInterface
     */
    private $authenticated_user;

    /**
     * @var AuthenticationResultInterface
     */
    private $authenticated_with;

    /**
     * AuthenticationTransport constructor.
     *
     * @param AdapterInterface              $adapter
     * @param AuthenticatedUserInterface    $authenticated_user
     * @param AuthenticationResultInterface $authenticated_with
     * @param mixed                         $payload
     */
    public function __construct(AdapterInterface $adapter, AuthenticatedUserInterface $authenticated_user, AuthenticationResultInterface $authenticated_with, $payload = null)
    {
        parent::__construct($adapter, $payload);

        $this->authenticated_user = $authenticated_user;
        $this->authenticated_with = $authenticated_with;
    }

    public function getAuthenticatedUser(): ?AuthenticatedUserInterface
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

    public function isEmpty(): bool
    {
        return empty($this->authenticated_user) && empty($this->authenticated_with);
    }
}
