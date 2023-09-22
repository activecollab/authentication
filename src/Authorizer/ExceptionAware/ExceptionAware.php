<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer\ExceptionAware;

use ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler\DelegatesToHandlerInterface;

trait ExceptionAware
{
    /**
     * {@inheritdoc}
     */
    public function handleException(array $credentials, $error_or_exception)
    {
        if ($this instanceof DelegatesToHandlerInterface && $this->getExceptionHandler()) {
            return $this->getExceptionHandler()->handleException($credentials, $error_or_exception);
        }

        return null;
    }
}
