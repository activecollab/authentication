<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer\ExceptionAware;

use ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler\DelegatesToHandlerInterface;
use Throwable;

trait ExceptionAware
{
    public function handleException(array $credentials, Throwable $error_or_exception): void
    {
        if (!$this instanceof DelegatesToHandlerInterface) {
            return;
        }

        $this->getExceptionHandler()->handleException($credentials, $error_or_exception);
    }
}
