<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Exception;

use Exception as PhpException;

/**
 * @package ActiveCollab\Authentication\Exception
 */
class ResetPasswordException extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = 'Invalid reset code', $code = 0, PhpException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
