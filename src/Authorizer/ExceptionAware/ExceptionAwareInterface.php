<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer\ExceptionAwareAware;

use Exception;
use Throwable;

/**
 * @package ActiveCollab\Authentication\Authorizer\ExceptionAwareAware
 */
interface ExceptionAwareInterface
{
    /**
     * @param  Throwable|Exception $error_or_exception
     * @return mixed
     */
    public function handleException($error_or_exception);
}
