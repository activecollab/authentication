<?php

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Exception\InvalidAuthenticateRequest;
use ActiveCollab\Authentication\Exception\InvalidPassword;
use ActiveCollab\Authentication\Exception\InvalidToken;
use ActiveCollab\Authentication\Exception\UserNotFound;
use ActiveCollab\Authentication\Test\Token\Token;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
class AuthorizationBearer implements AdapterInterface
{
    /**
     * @var RepositoryInterface
     */
    private $users_repository;

    /**
     * @param RepositoryInterface $users_repository
     */
    public function __construct(RepositoryInterface $users_repository)
    {
        $this->users_repository = $users_repository;
    }

    /**
     * Initialize authentication layer and see if we have a user who's already logged in
     *
     * @param  ServerRequestInterface          $request
     * @return AuthenticatedUserInterface|null
     */
    public function initialize(ServerRequestInterface $request)
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
        $credentials = $request->getParsedBody();

        if (!is_array($credentials) || empty($credentials['username']) || empty($credentials['password'])) {
            throw new InvalidAuthenticateRequest();
        }

        $user = $this->users_repository->findByUsername($credentials['username']);

        if (!$user) {
            throw new UserNotFound();
        }

        if (!$user->isValidPassword($credentials['password'])) {
            throw new InvalidPassword();
        }

        if (!$user->canAuthenticate()) {
            throw new UserNotFound();
        }

        return new Token(sha1(time()));
    }
}