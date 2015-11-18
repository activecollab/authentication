<?php

namespace ActiveCollab\Authentication\Test\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * @package ActiveCollab\Authentication\Test\Http
 */
class Response implements ResponseInterface
{
    use Common;

    public function getStatusCode()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function getReasonPhrase()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }
}
