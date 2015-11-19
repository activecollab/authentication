<?php

namespace ActiveCollab\Authentication\Exception;

/**
 * @package ActiveCollab\Authentication\Exception
 */
class InvalidAuthenticateRequest extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = "Authentication request data not valid", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
