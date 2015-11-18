<?php

namespace ActiveCollab\Authentication\Test\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

trait Common
{
    private $headers = [];
    public function getProtocolVersion()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function withProtocolVersion($version)
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function hasHeader($name)
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    /**
     * @param $name
     * @param $value
     * @return RequestInterface
     */
    public function withHeader($name, $value)
    {
        $clone = clone($this);

        $clone->headers[$name] = [$value];

        return $clone;
    }

    public function withAddedHeader($name, $value)
    {
        $clone = clone($this);

        if (! isset($clone->headers[$name])) {
            $clone->headers[$name] = [];
        }

        $clone->headers[$name][] = $value;

        return $clone;
    }

    public function withoutHeader($name)
    {
        $clone = clone($this);

        if (isset($clone->headers[$name])) {
            unset($clone->headers[$name]);
        }

        return $clone;
    }

    public function getBody()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function withBody(StreamInterface $body)
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function getHeaders()
    {
        throw new \RuntimeException("This method has not been implemented.");
    }

    public function getHeader($name)
    {
        if (! isset($this->headers[$name])) {
            return [];
        }

        return $this->headers[$name];
    }

    public function getHeaderLine($name)
    {
        if (array_key_exists($name, $this->headers)) {
            return implode(',', $this->headers[$name]);
        } else {
            return '';
        }
    }

    public function getHeaderLines($name)
    {
        if (! isset($this->headers[$name])) {
            return [];
        }

        return $this->headers[$name];
    }
}
