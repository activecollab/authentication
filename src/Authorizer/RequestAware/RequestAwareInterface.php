<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer\RequestAware;

use ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessorInterface;

/**
 * @package ActiveCollab\Authentication\Authorizer\RequestAware
 */
interface RequestAwareInterface
{
    /**
     * @return RequestProcessorInterface
     */
    public function getRequestProcessor();
}
