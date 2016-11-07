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
class CurrentTimestamp implements CurrentTimestampInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCurrentTimestamp()
    {
        return time();
    }
}
