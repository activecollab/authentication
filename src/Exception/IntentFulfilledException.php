<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Exception;

use Exception;

class IntentFulfilledException extends RuntimeException
{
    public function __construct(
        string $message = 'Intent already fulfilled',
        int $code = 0,
        Exception $previous = null,
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
