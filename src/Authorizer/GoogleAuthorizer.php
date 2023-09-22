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
    private RepositoryInterface $user_repository;
    private Google_Client $google_client;
    private string $client_id;
    private array $user_profile;

    /**
     * GoogleAuthorizer constructor.
     *
     * @param RepositoryInterface $user_repository
     * @param Google_Client $google_client
     * @param string $client_id
     * @param ExceptionHandlerInterface|null $exception_handler
     */
    public function __construct(
        RepositoryInterface $user_repository,
        Google_Client $google_client,
        string $client_id,
        ExceptionHandlerInterface $exception_handler = null
    ) {
        $this->user_repository = $user_repository;
        $this->google_client = $google_client;
        $this->client_id = $client_id;
        $this->user_profile = [];
        $this->setExceptionHandler($exception_handler);
    }

    /**
     * Credentials should be in array format with keys: token and username.
     * Example: ['token' => '123abc', 'username' => 'john.doe.123@gmail.com'].
     *
     * {@inheritdoc}
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

    /**
     * @return array
     */
    public function getUserProfile(): array
    {
        return $this->user_profile;
    }

    /**
     * @param array $payload
     * @param string|null $username
     */
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

    /**
     * @param AuthenticatedUserInterface|null $user
     */
    private function verifyUser(AuthenticatedUserInterface $user = null): void
    {
        if (!$user || !$user->canAuthenticate()) {
            throw new UserNotFoundException();
        }
    }

    /**
     * @return array
     */
    private function getDomains(): array
    {
        return ['accounts.google.com', 'https://accounts.google.com'];
    }
}
