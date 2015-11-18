<?php

namespace ActiveCollab\Authentication\AuthenticationResultInterface;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7;

/**
 * @package ActiveCollab\Authentication\AuthenticationResultInterface
 */
trait Implementation
{
    /**
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function toResponse(ResponseInterface $response)
    {
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->withBody(Psr7\stream_for(json_encode($this)));
    }
}
