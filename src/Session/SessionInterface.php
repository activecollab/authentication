<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Session;

use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;

interface SessionInterface extends AuthenticationResultInterface
{
    const SESSION_DURATION_SHORT = 'short';
    const SESSION_DURATION_LONG = 'long';
    const DEFAULT_SESSION_DURATION = self::SESSION_DURATION_SHORT;

    const SESSION_DURATIONS = [
        self::SESSION_DURATION_SHORT,
        self::SESSION_DURATION_LONG,
    ];

    public function getSessionId(): string;
    public function getSessionTtl(): int;

    /**
     * Extend session for the set TTL while using $timestamp as reference (defaults to time() when empty).
     */
    public function extendSession(int $reference_timestamp = null): void;
}
