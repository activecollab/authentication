<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessingResult;

class RequestProcessingResult implements RequestProcessingResultInterface
{
    public function __construct(
        private array $credentials,
        private mixed $default_payload = null,
    )
    {
    }

    public function getCredentials(): array
    {
        return $this->credentials;
    }

    public function getDefaultPayload(): mixed
    {
        return $this->default_payload;
    }
}
