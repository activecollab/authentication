<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\Exception\InvalidTokenException;
use ActiveCollab\Authentication\Token\RepositoryInterface as TokenRepositoryInterface;
use ActiveCollab\Authentication\Token\TokenInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
class TokenBearer extends Adapter
{
    /**
     * @var UserRepositoryInterface
     */
    private $user_repository;

    /**
     * @var TokenRepositoryInterface
     */
    private $token_repository;

    /**
     * @param UserRepositoryInterface  $user_repository
     * @param TokenRepositoryInterface $token_repository
     */
    public function __construct(UserRepositoryInterface $user_repository, TokenRepositoryInterface $token_repository)
    {
        $this->user_repository = $user_repository;
        $this->token_repository = $token_repository;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ServerRequestInterface $request, &$authenticated_with = null)
    {
        $authorization = $request->getHeaderLine('Authorization');

        if (!empty($authorization) && substr($authorization, 0, 7) === 'Bearer ') {
            $token_id = trim(substr($authorization, 7));

            if ($token_id === null || $token_id === '') {
                throw new InvalidTokenException();
            }

            if ($token = $this->token_repository->getById($token_id)) {
                if ($user = $token->getAuthenticatedUser($this->user_repository)) {
                    $this->token_repository->recordUsage($token);
                    $authenticated_with = $token;

                    return $user;
                }
            }

            throw new InvalidTokenException();
        }

        return null;
    }

    /**
     * Authenticate with given credential agains authentication source.
     *
     * @param  ServerRequestInterface        $request
     * @param  bool                          $check_password
     * @return AuthenticationResultInterface
     */
    public function authenticate(ServerRequestInterface $request, $check_password = true)
    {
        return $this->token_repository->issueToken($this->getUserFromCredentials(
            $this->user_repository,
            $this->getAuthenticationCredentialsFromRequest($request),
            $check_password
        ));
    }

    /**
     * Terminate an instance that was used to authenticate a user.
     *
     * @param AuthenticationResultInterface $authenticated_with
     */
    public function terminate(AuthenticationResultInterface $authenticated_with)
    {
        if ($authenticated_with instanceof TokenInterface) {
            $this->token_repository->terminateToken($authenticated_with);
        } else {
            throw new InvalidArgumentException('Instance is not a browser session');
        }
    }
}
