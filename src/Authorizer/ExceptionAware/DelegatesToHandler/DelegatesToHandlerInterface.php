<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler;

use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionHandler\ExceptionHandlerInterface;

interface DelegatesToHandlerInterface
{
    /**
     * Return exception handler.
     *
     * @return ExceptionHandlerInterface|null
     */
    public function getExceptionHandler();
}
