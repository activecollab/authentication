<?php

namespace ActiveCollab\Authentication;

use Psr\Http\Message\ResponseInterface;

/**
 * @package ActiveCollab\Authentication
 */
interface AuthenticationResultInterface extends \JsonSerializable
{
    /**
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function toResponse(ResponseInterface $response);
}
