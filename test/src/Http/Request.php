<?php

namespace ActiveCollab\Authentication\Test\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @package ActiveCollab\Authentication\Test\Http
 */
class Request implements RequestInterface
{
    use Common;

    public function getRequestTarget()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function withRequestTarget($requestTarget)
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function getMethod()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function withMethod($method)
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function getUri()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        throw new \RuntimeException("This method has not been implemented.");
    }
}
