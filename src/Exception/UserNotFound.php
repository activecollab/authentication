<?php

/*
 * This file is part of the Active Collab ID project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Exception;

/**
 * @package ActiveCollab\Authentication\Exception
 */
class UserNotFound extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = 'User not found', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
