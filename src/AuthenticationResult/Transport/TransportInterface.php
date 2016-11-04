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
interface TransportInterface
{
    /**
     * Return adapter which produced this initialization result.
     *
     * @return AdapterInterface
     */
    public function getAdapter();

    /**
     * @return AuthenticatedUserInterface|null
     */
    public function getAuthenticatedUser();

    /**
     * @return AuthenticationResultInterface|null
     */
    public function getAuthenticatedWith();

    /**
     * Return an array of any additional arguments that adapter wants to pass alogside authorization results.
     *
     * @return array
     */
    public function getAdditionalArguments();

    /**
     * @return bool
     */
    public function isEmpty();
}
