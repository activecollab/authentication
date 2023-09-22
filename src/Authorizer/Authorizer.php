<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler\DelegatesToHandler as DelegatesToExceptionHandlerImplementation;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\DelegatesToHandler\DelegatesToHandlerInterface;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionAware as ExceptionAwareImplementation;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionAwareInterface;

abstract class Authorizer implements
    AuthorizerInterface,
    DelegatesToHandlerInterface,
    ExceptionAwareInterface
{
    use DelegatesToExceptionHandlerImplementation, ExceptionAwareImplementation;
}
