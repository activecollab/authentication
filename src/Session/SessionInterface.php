<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Session;

use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;

/**
 * @package ActiveCollab\Authentication\Session
 */
interface SessionInterface extends AuthenticationResultInterface
{
    const SESSION_DURATION_SHORT = 'short';
    const SESSION_DURATION_LONG = 'long';
    const DEFAULT_SESSION_DURATION = self::SESSION_DURATION_SHORT;

    const SESSION_DURATIONS = [
        self::SESSION_DURATION_SHORT,
        self::SESSION_DURATION_LONG,
    ];

    /**
     * @return string
     */
    public function getSessionId();

    /**
     * @return int
     */
    public function getSessionTtl();

    /**
     * Extend session for the set TTL while using $timestamp as reference (defaults to time() when empty).
     *
     * @param int|null $reference_timestamp
     */
    public function extendSession($reference_timestamp = null);
}
