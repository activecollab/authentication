<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer\ExceptionAware;

use Exception;
use Throwable;

/**
 * @package ActiveCollab\Authentication\Authorizer\ExceptionAware
 */
interface ExceptionAwareInterface
{
    /**
     * @param  array               $credentials
     * @param  Throwable|Exception $error_or_exception
     * @return void|mixed
     */
    public function handleException(array $credentials, $error_or_exception);
}
