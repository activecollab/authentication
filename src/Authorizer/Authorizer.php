<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler\DelegatesToHandler as DelegatesToExceptionHandler;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler\DelegatesToHandlerInterface;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionAware;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionHandler\ExceptionHandlerInterface;

/**
 * @package ActiveCollab\Authentication\Authorizer
 */
abstract class Authorizer implements
    AuthorizerInterface,
    DelegatesToHandlerInterface,
    ExceptionHandlerInterface
{
    use DelegatesToExceptionHandler, ExceptionAware;
}
