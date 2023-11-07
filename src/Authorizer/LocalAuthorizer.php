<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionHandler\ExceptionHandlerInterface;
use ActiveCollab\Authentication\Exception\InvalidPasswordException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;

class LocalAuthorizer extends Authorizer
{
    use CredentialFieldsCheckTrait;

    public function __construct(
        private RepositoryInterface $user_repository,
        private string $username_format = AuthorizerInterface::USERNAME_FORMAT_ANY,
        ExceptionHandlerInterface $exception_handler = null,
        private bool $supports_second_factor = true,
    )
    {
        parent::__construct($supports_second_factor);

        $this->setExceptionHandler($exception_handler);
    }

    /**
     * Credentials should be in array format with keys: token and username.
     * Example: ['username' => 'john.doe.123@gmail.com', 'password' => '123abc'].
     */
    public function verifyCredentials(array $credentials): ?AuthenticatedUserInterface
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

    private function verifyUser(?AuthenticatedUserInterface $user, string $password): void
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
