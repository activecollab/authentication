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
