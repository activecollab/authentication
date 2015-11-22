<?php

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Exception\InvalidToken;
use ActiveCollab\Authentication\Token\RepositoryInterface as TokenRepositoryInterface;
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
            $token = trim(substr($authorization, 7));

            if (empty($token)) {
                throw new InvalidToken();
            }

            if ($user = $this->users_repository->findByToken($token)) {
                $this->users_repository->recordTokenUsage($token);

                return $user;
            } else {
                throw new InvalidToken();
            }
        }
    }

    /**
     * Authenticate with given credential agains authentication source
     *
     * @param  ServerRequestInterface        $request
     * @return AuthenticationResultInterface
     */
    public function authenticate(ServerRequestInterface $request)
    {
        return $this->tokens_repository->issueToken($this->getUserFromCredentials($this->users_repository, $this->getAuthenticationCredentialsFromRequest($request)));
    }
}