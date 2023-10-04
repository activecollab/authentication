<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Exception;

use Exception;
use RuntimeException as PhpRuntimeException;

class RuntimeException extends PhpRuntimeException implements ExceptionInterface
{
    public function __construct(
        string $message = null,
        int $code = 0,
        Exception $previous = null,
    )
    {
        parent::__construct(
            $message ?? $this->getAuthExceptionMessage(),
            $code,
            $previous,
        );
    }

    protected function getAuthExceptionMessage(): string
    {
        return 'Runtime exception.';
    }
}
