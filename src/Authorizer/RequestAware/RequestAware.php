<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer\RequestAware;

use ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessorInterface;

trait RequestAware
{
    private ?RequestProcessorInterface $request_processor = null;

    public function getRequestProcessor(): ?RequestProcessorInterface
    {
        return $this->request_processor;
    }
    
    protected function setRequestProcessor(RequestProcessorInterface $request_processor = null): static
    {
        $this->request_processor = $request_processor;

        return $this;
    }
}
