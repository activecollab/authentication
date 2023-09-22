<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Exception;

use Exception as PhpException;

class InvalidCredentialsException extends RuntimeException
{
    public function __construct(
        $message = 'Invalid credentials provided',
        $code = 0,
        PhpException $previous = null,
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
