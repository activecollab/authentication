<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Exception\RuntimeException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;
use Google_Client;

/**
 * @package ActiveCollab\Authentication\Authorizer
 */
class GoogleAuthorizer implements AuthorizerInterface
{
    use CredentialFieldsCheckTrait;

    /**
     * @var RepositoryInterface
     */
    private $user_repository;

    /**
     * @var Google_Client
     */
    private $google_client;

    /**
     * @var string
     */
    private $client_id;

    /**
     * @var array
     */
    private $user_profile;

    /**
     * @param RepositoryInterface $user_repository
     * @param Google_Client       $google_client
     * @param string              $client_id
     */
    public function __construct(RepositoryInterface $user_repository, Google_Client $google_client, $client_id)
    {
        $this->user_repository = $user_repository;
        $this->google_client = $google_client;
        $this->client_id = $client_id;
        $this->user_profile = [];
    }

    /**
     * Credentials should be in array format with keys: token and username.
     * Example: ['token' => '123abc', 'username' => 'john.doe.123@gmail.com'].
     *
     * {@inheritdoc}
     */
    public function verifyCredentials(array $credentials)
    {
        $this->verifyRequiredFields($credentials, ['token', 'username']);

        $token = $credentials['token'];
        $username = $credentials['username'];

        $payload = $this->google_client->verifyIdToken($token)->getAttributes()['payload'];
        $this->verifyGoogleProfile($payload, $username);

        $user = $this->user_repository->findByUsername($username);
        $this->verifyUser($user);

        $this->user_profile = $payload;

        return $user;
    }

    /**
     * @return array
     */
    public function getUserProfile()
    {
        return $this->user_profile;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogin(array $payload)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onLogout(array $payload)
    {
    }

    /**
     * @param array  $payload
     * @param string $username
     */
    private function verifyGoogleProfile(array $payload, $username)
    {
        if ($this->client_id !== $payload['aud']) {
            throw new RuntimeException('Unrecognized google_client');
        }

        if (!in_array($payload['iss'], $this->getDomains())) {
            throw new RuntimeException('Wrong issuer');
        }

        if ($username !== $payload['email']) {
            throw new RuntimeException('Email is not verified by Google');
        }
    }

    /**
     * @param AuthenticatedUserInterface|null $user
     */
    private function verifyUser(AuthenticatedUserInterface $user = null)
    {
        if (!$user || !$user->canAuthenticate()) {
            throw new UserNotFoundException();
        }
    }

    /**
     * @return array
     */
    private function getDomains()
    {
        return ['accounts.google.com', 'https://accounts.google.com'];
    }
}
