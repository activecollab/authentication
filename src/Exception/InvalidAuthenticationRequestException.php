<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Exception;

use Exception;

class InvalidAuthenticationRequestException extends RuntimeException
{
    public function __construct(
        string $message = 'Authentication request data not valid',
        int $code = 0,
        Exception $previous = null,
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
