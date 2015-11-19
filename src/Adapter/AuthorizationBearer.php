<?php

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Exception\InvalidTokenException;
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
                throw new InvalidTokenException();
            }

            if ($user = $this->users_repository->findByToken($token)) {
                $this->users_repository->recordTokenUsage($token);

                return $user;
            } else {
                throw new InvalidTokenException();
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
        $request->getBody();
    }
}