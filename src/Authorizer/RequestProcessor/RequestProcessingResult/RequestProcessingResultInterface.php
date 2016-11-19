<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessingResult;

/**
 * @package ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessingResult
 */
interface RequestProcessingResultInterface
{
    /**
     * @return array
     */
    public function getCredentials();

    /**
     * @return mixed
     */
    public function getDefaultPayload();
}
