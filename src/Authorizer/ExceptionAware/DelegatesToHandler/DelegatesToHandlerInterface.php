<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler;

use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionHandler\ExceptionHandlerInterface;

/**
 * @package ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler
 */
interface DelegatesToHandlerInterface
{
    /**
     * Return exception handler.
     *
     * @return ExceptionHandlerInterface|null
     */
    public function getExceptionHandler();
}
