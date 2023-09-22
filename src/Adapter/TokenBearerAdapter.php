<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Deauthentication\DeauthenticationTransport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Exception\InvalidTokenException;
use ActiveCollab\Authentication\Token\RepositoryInterface as TokenRepositoryInterface;
use ActiveCollab\Authentication\Token\TokenInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

class TokenBearerAdapter extends Adapter implements TokenBearerAdapterInterface
{
    public function __construct(
        private UserRepositoryInterface $user_repository,
        private TokenRepositoryInterface $token_repository,
    )
    {
    }

    public function initialize(ServerRequestInterface $request): ?TransportInterface
    {
        if (!$request->hasHeader('Authorization')) {
            return null;
        }

        $authorization = $request->getHeaderLine('Authorization');

        if (empty($authorization) || mb_substr($authorization, 0, 6) !== 'Bearer') {
            return null;
        }

        $token_id = trim(mb_substr($authorization, 7));

        if (empty($token_id)) {
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

    public function authenticate(
        AuthenticatedUserInterface $authenticated_user,
        array $credentials = []
    ): AuthenticationResultInterface
    {
        return $this->token_repository->issueToken($authenticated_user, $credentials);
    }

    public function terminate(AuthenticationResultInterface $authenticated_with): TransportInterface
    {
        if (!$authenticated_with instanceof TokenInterface) {
            throw new InvalidArgumentException('Instance is not a token');
        }

        $this->token_repository->terminateToken($authenticated_with);

        return new DeauthenticationTransport($this);
    }
}
