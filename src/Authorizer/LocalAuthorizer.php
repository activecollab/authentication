<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionHandler\ExceptionHandlerInterface;
use ActiveCollab\Authentication\Exception\InvalidPasswordException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;

/**
 * @package ActiveCollab\Authentication\Authorizer
 */
class LocalAuthorizer extends Authorizer
{
    use CredentialFieldsCheckTrait;

    /**
     * @var RepositoryInterface
     */
    private $user_repository;

    /**
     * @var bool
     */
    private $username_format;

    /**
     * LocalAuthorizer constructor.
     *
     * @param RepositoryInterface            $user_repository
     * @param string                         $username_format
     * @param ExceptionHandlerInterface|null $exception_handler
     */
    public function __construct(RepositoryInterface $user_repository, $username_format = AuthorizerInterface::USERNAME_FORMAT_ANY, ExceptionHandlerInterface $exception_handler = null)
    {
        $this->user_repository = $user_repository;
        $this->username_format = $username_format;
        $this->setExceptionHandler($exception_handler);
    }

    /**
     * Credentials should be in array format with keys: token and username.
     * Example: ['username' => 'john.doe.123@gmail.com', 'password' => '123abc'].
     *
     * {@inheritdoc}
     */
    public function verifyCredentials(array $credentials)
    {
        $this->verifyRequiredFields($credentials, ['username', 'password']);

        switch ($this->username_format) {
            case AuthorizerInterface::USERNAME_FORMAT_ALPHANUM:
                $this->verifyAlphanumFields($credentials, ['username']);
                break;
            case AuthorizerInterface::USERNAME_FORMAT_EMAIL:
                $this->verifyEmailFields($credentials, ['username']);
                break;
        }

        $user = $this->user_repository->findByUsername($credentials['username']);

        $this->verifyUser($user, $credentials['password']);

        return $user;
    }

    /**
     * @param AuthenticatedUserInterface|null $user
     * @param string                          $password
     */
    private function verifyUser(AuthenticatedUserInterface $user = null, $password)
    {
        if (!$user) {
            throw new UserNotFoundException();
        }

        if (!$user->isValidPassword($password)) {
            throw new InvalidPasswordException();
        }

        if (!$user->canAuthenticate()) {
            throw new UserNotFoundException();
        }
    }
}
