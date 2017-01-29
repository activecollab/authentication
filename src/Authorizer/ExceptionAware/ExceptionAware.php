<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer\ExceptionAware;

use ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler\DelegatesToHandlerInterface;

/**
 * @package ActiveCollab\Authentication\Authorizer\ExceptionAware
 */
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
