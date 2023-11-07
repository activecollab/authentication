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
use ActiveCollab\Authentication\Exception\RuntimeException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;
use Google_Client;

class GoogleAuthorizer extends Authorizer
{
    use CredentialFieldsCheckTrait;

    private array $user_profile = [];

    public function __construct(
        private RepositoryInterface $user_repository,
        private Google_Client $google_client,
        private string $client_id,
        ExceptionHandlerInterface $exception_handler = null,
        private bool $supports_second_factor = true,
    ) {
        parent::__construct($supports_second_factor);

        $this->setExceptionHandler($exception_handler);
    }

    /**
     * Credentials should be in array format with keys: token and username.
     * Example: ['token' => '123abc', 'username' => 'john.doe.123@gmail.com'].
     */
    public function verifyCredentials(array $credentials): ?AuthenticatedUserInterface
    {
        $this->verifyRequiredFields($credentials, ['token']);
        $token = $credentials['token'];

        $payload = $this->google_client->verifyIdToken($token);
        $username = $payload['email'] ?? $credentials['username'];

        $this->verifyGoogleProfile($payload, $credentials['username'] ?? null);
        $this->user_profile = $payload;

        $user = $this->user_repository->findByUsername($username);
        $this->verifyUser($user);

        return $user;
    }

    public function getUserProfile(): array
    {
        return $this->user_profile;
    }

    private function verifyGoogleProfile(array $payload, ?string $username): void
    {
        if ($this->client_id !== $payload['aud']) {
            throw new RuntimeException('Unrecognized google_client');
        }

        if (!in_array($payload['iss'], $this->getDomains())) {
            throw new RuntimeException('Wrong issuer');
        }

        if ($username && $username !== $payload['email']) {
            throw new RuntimeException('Email is not verified by Google');
        }
    }

    private function verifyUser(AuthenticatedUserInterface $user = null): void
    {
        if (!$user || !$user->canAuthenticate()) {
            throw new UserNotFoundException();
        }
    }

    private function getDomains(): array
    {
        return [
            'accounts.google.com',
            'https://accounts.google.com',
        ];
    }
}
