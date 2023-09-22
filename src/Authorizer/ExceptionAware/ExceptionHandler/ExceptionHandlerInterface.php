<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionHandler;

use Throwable;

interface ExceptionHandlerInterface
{
    public function handleException(
        array $credentials,
        Throwable $error_or_exception,
    ): void;
}
