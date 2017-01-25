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
trait DelegatesToHandler
{
    /**
     * @var ExceptionHandlerInterface
     */
    private $exception_handler;

    /**
     * {@inheritdoc}
     */
    public function getExceptionHandler()
    {
        return $this->exception_handler;
    }

    /**
     * @param  ExceptionHandlerInterface|null $exception_handler
     * @return $this
     */
    protected function &setExceptionHandler(ExceptionHandlerInterface $exception_handler = null)
    {
        $this->exception_handler = $exception_handler;

        return $this;
    }
}
