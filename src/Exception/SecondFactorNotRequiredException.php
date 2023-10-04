<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Exception;

class SecondFactorNotRequiredException extends RuntimeException
{
    protected function getAuthExceptionMessage(): string
    {
        return 'Second factor authentication not required for user.';
    }
}
