<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @package ActiveCollab\Authentication
 */
interface AuthenticationResultInterface extends \JsonSerializable
{
    /**
     * Get authenticated user from the repository.
     *
     * @param  UserRepositoryInterface    $repository
     * @return AuthenticatedUserInterface
     */
    public function getAuthenticatedUser(UserRepositoryInterface $repository);

    /**
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function toResponse(ResponseInterface $response);
}
