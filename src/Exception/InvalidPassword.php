<?php

namespace ActiveCollab\Authentication\Exception;

/**
 * @package ActiveCollab\Authentication\Exception
 */
class InvalidPassword extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = "Password not valid", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
