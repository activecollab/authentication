<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Exception;

class InvalidAuthenticationRequestException extends RuntimeException
{
    protected function getAuthExceptionMessage(): string
    {
        return 'Authentication request data not valid.';
    }
}
