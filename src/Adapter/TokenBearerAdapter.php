<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Deauthentication\DeauthenticationTransport;
use ActiveCollab\Authentication\Exception\InvalidTokenException;
use ActiveCollab\Authentication\Token\RepositoryInterface as TokenRepositoryInterface;
use ActiveCollab\Authentication\Token\TokenInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
class TokenBearerAdapter extends Adapter
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
    public function initialize(ServerRequestInterface $request)
    {
        if (!$request->hasHeader('Authorization')) {
            return null;
        }

        $authorization = $request->getHeaderLine('Authorization');

        if (empty($authorization) || substr($authorization, 0, 6) !== 'Bearer') {
            return null;
        }

        $token_id = trim(substr($authorization, 7));

        if ($token_id === null || $token_id === '') {
            throw new InvalidTokenException();
        }

        if ($token = $this->token_repository->getById($token_id)) {
            if ($user = $token->getAuthenticatedUser($this->user_repository)) {
                $this->token_repository->recordUsageByToken($token);

                return new AuthenticationTransport($this, $user, $token);
            }
        }

        throw new InvalidTokenException();
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(AuthenticatedUserInterface $authenticated_user, array $credentials = [])
    {
        return $this->token_repository->issueToken($authenticated_user, $credentials);
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(AuthenticationResultInterface $authenticated_with)
    {
        if (!$authenticated_with instanceof TokenInterface) {
            throw new InvalidArgumentException('Instance is not a token');
        }

        $this->token_repository->terminateToken($authenticated_with);

        return new DeauthenticationTransport($this);
    }
}
