<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;
use ActiveCollab\Authentication\Exception\InvalidPasswordException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
abstract class Adapter implements AdapterInterface
{
    /**
     * Return authentication credentials from request.
     *
     * @param  ServerRequestInterface $request
     * @param  bool                   $check_password
     * @return array
     */
    protected function getAuthenticationCredentialsFromRequest(ServerRequestInterface $request, $check_password = true)
    {
        $credentials = $request->getParsedBody();

        if (!is_array($credentials) || empty($credentials['username']) || ($check_password && empty($credentials['password']))) {
            throw new InvalidAuthenticationRequestException();
        }

        return $credentials;
    }

    /**
     * @param  RepositoryInterface        $repository
     * @param  array                      $credentials
     * @param  bool                       $check_password
     * @return AuthenticatedUserInterface
     */
    protected function getUserFromCredentials(RepositoryInterface $repository, array $credentials, $check_password = true)
    {
        $user = $repository->findByUsername($credentials['username']);

        if (!$user) {
            throw new UserNotFoundException();
        }

        if ($check_password && !$user->isValidPassword($credentials['password'])) {
            throw new InvalidPasswordException();
        }

        if (!$user->canAuthenticate()) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
