<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\AuthenticationResultInterface;
use ActiveCollab\Authentication\Exception\InvalidToken;
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
    private $users_repository;

    /**
     * @var TokenRepositoryInterface
     */
    private $tokens_repository;

    /**
     * @param UserRepositoryInterface  $users_repository
     * @param TokenRepositoryInterface $tokens_repository
     */
    public function __construct(UserRepositoryInterface $users_repository, TokenRepositoryInterface $tokens_repository)
    {
        $this->users_repository = $users_repository;
        $this->tokens_repository = $tokens_repository;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ServerRequestInterface $request, &$authenticated_with = null)
    {
        $authorization = $request->getHeaderLine('Authorization');

        if (!empty($authorization) && substr($authorization, 0, 7) == 'Bearer ') {
            $token_id = trim(substr($authorization, 7));

            if (empty($token_id)) {
                throw new InvalidToken();
            }

            if ($token = $this->tokens_repository->getById($token_id)) {
                if ($user = $token->getAuthenticatedUser($this->users_repository)) {
                    $this->tokens_repository->recordUsage($token);
                    $authenticated_with = $token;

                    return $user;
                }
            }

            throw new InvalidToken();
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
        return $this->tokens_repository->issueToken($this->getUserFromCredentials($this->users_repository, $this->getAuthenticationCredentialsFromRequest($request), $check_password));
    }

    /**
     * Terminate an instance that was used to authenticate a user.
     *
     * @param AuthenticationResultInterface $authenticated_with
     */
    public function terminate(AuthenticationResultInterface $authenticated_with)
    {
        if ($authenticated_with instanceof TokenInterface) {
            $this->tokens_repository->terminateToken($authenticated_with);
        } else {
            throw new InvalidArgumentException('Instance is not a token');
        }
    }
}
