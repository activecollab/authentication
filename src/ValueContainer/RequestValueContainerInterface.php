<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\ValueContainer;

use ActiveCollab\ValueContainer\ValueContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\ValueContainer
 */
interface RequestValueContainerInterface extends ValueContainerInterface
{
    /**
     * @param  ServerRequestInterface $request
     * @return $this
     */
    public function &setRequest(ServerRequestInterface $request);
}
