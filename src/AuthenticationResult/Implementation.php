<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\AuthenticationResult;

use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;

/**
 * @package ActiveCollab\Authentication\AuthenticationResult
 */
trait Implementation
{
    /**
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function toResponse(ResponseInterface $response)
    {
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(Psr7\stream_for(json_encode($this)));
    }
}
