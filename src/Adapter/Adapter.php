<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
abstract class Adapter implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function finalize(ServerRequestInterface $request, ResponseInterface $response, TransportInterface $transport)
    {
        return [$request, $response];
    }
}
