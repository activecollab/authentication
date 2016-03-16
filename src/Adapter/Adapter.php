<?php

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\Exception\InvalidAuthenticateRequest;
use ActiveCollab\Authentication\Exception\InvalidPassword;
use ActiveCollab\Authentication\Exception\UserNotFound;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
abstract class Adapter implements AdapterInterface
{
    /**
     * Return authentication credentials from request
     *
     * @param  ServerRequestInterface $request
     * @return array
     */
    protected function getAuthenticationCredentialsFromRequest(ServerRequestInterface $request)
    {
        $credentials = $request->getParsedBody();

        if (!is_array($credentials) || empty($credentials['username']) || empty($credentials['password'])) {
            throw new InvalidAuthenticateRequest();
        }

        return $credentials;
    }

    /**
     * @param  UserRepositoryInterface    $repository
     * @param  array                      $credentials
     * @param  bool                       $checkPassword
     * @return AuthenticatedUserInterface
     */
    protected function getUserFromCredentials(UserRepositoryInterface $repository, array $credentials, $checkPassword = true)
    {
        $user = $repository->findByUsername($credentials['username']);

        if (!$user) {
            throw new UserNotFound();
        }

        if ($checkPassword && !$user->isValidPassword($credentials['password'])) {
            throw new InvalidPassword();
        }

        if (!$user->canAuthenticate()) {
            throw new UserNotFound();
        }

        return $user;
    }
}
