<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter\InitializationResult;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;

/**
 * @package ActiveCollab\Authentication\AuthenticationResult\Transport
 */
interface InitializationResultInterface
{
    /**
     * @return AuthenticatedUserInterface
     */
    public function getAuthenticatedUser();

    /**
     * @return AuthenticationResultInterface
     */
    public function getAuthenticatedWith();

    /**
     * Return an array of any additional arguments that adapter wants to pass alogside authorization results.
     *
     * @return array
     */
    public function getAdditionalArguments();
}
