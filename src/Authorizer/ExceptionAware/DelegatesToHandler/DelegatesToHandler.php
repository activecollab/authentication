<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler;

use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionHandler\ExceptionHandlerInterface;

trait DelegatesToHandler
{
    private ?ExceptionHandlerInterface $exception_handler = null;

    public function getExceptionHandler(): ?ExceptionHandlerInterface
    {
        return $this->exception_handler;
    }

    protected function setExceptionHandler(ExceptionHandlerInterface $exception_handler = null): static
    {
        $this->exception_handler = $exception_handler;

        return $this;
    }
}
