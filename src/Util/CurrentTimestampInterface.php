<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Util;

/**
 * @package ActiveCollab\Authentication\Util
 */
interface CurrentTimestampInterface
{
    /**
     * Return current timestamp.
     *
     * @return int
     */
    public function getCurrentTimestamp();
}
