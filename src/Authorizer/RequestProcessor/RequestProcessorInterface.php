<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer\RequestProcessor;

use ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessingResult\RequestProcessingResultInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RequestProcessorInterface
{
    public function processRequest(ServerRequestInterface $request): RequestProcessingResultInterface;
}
