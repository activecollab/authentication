<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Exception;

class InvalidTokenException extends RuntimeException
{
    protected function getAuthExceptionMessage(): string
    {
        return 'Authorization token is not valid.';
    }
}
