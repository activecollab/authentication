<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer\ExceptionAware;

use Exception;
use Throwable;

interface ExceptionAwareInterface
{
    /**
     * @param  array               $credentials
     * @param  Throwable|Exception $error_or_exception
     * @return void|mixed
     */
    public function handleException(array $credentials, $error_or_exception);
}
