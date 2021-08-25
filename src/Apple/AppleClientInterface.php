<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Apple;

use Azimo\Apple\Auth\Service\AppleJwtFetchingServiceInterface;
use Azimo\Apple\Auth\Struct\JwtPayload;

interface AppleClientInterface
{
    public function getService(): AppleJwtFetchingServiceInterface;

    public function verifyIdToken(string $token): JwtPayload;
}
