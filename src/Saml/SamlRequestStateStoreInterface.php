<?php

declare(strict_types=1);

namespace ActiveCollab\Authentication\Saml;

interface SamlRequestStateStoreInterface
{
    /**
     * Store a pending AuthnRequest ID with a TTL in seconds.
     */
    public function save(string $request_id, int $ttl): void;

    /**
     * Find and atomically delete a pending request ID.
     *
     * Returns true if the ID was found and consumed, false otherwise.
     */
    public function consume(string $request_id): bool;
}
