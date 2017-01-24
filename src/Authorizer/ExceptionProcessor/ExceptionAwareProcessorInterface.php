<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer\ExceptionProcessor;

use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionAwareInterface;

/**
 * @package ActiveCollab\Authentication\Authorizer\ExceptionProcessor
 */
interface ExceptionAwareProcessorInterface
{
    /**
     * @return ExceptionAwareInterface
     */
    public function getExceptionProcessor();
}
