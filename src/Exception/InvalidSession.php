<?php

namespace ActiveCollab\Authentication\Exception;

/**
 * @package ActiveCollab\Authentication\Exception
 */
class InvalidSession extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = "Session ID is not valid", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
