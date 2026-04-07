<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\Saml\Fixtures;

use ActiveCollab\Authentication\Saml\SamlRequestStateStoreInterface;

class InMemorySamlRequestStateStore implements SamlRequestStateStoreInterface
{
    private array $pending = [];

    public function save(string $request_id, int $ttl): void
    {
        $this->pending[$request_id] = true;
    }

    public function consume(string $request_id): bool
    {
        if (isset($this->pending[$request_id])) {
            unset($this->pending[$request_id]);

            return true;
        }

        return false;
    }
}
