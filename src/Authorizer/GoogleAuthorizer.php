<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\Exception\RuntimeException;
use Exception;
use Google_Client;

/**
 * @package ActiveCollab\Authorizer
 */
class GoogleAuthorizer implements AuthorizerInterface
{
    /**
     * @var Google_Client
     */
    private $google_client;

    /**
     * @var string
     */
    private $client_id;

    /**
     * @param Google_Client $google_client
     * @param string        $client_id
     */
    public function __construct(Google_Client $google_client, $client_id)
    {
        $this->google_client = $google_client;
        $this->client_id = $client_id;
    }

    /**
     * Credentials should be in array format with keys: token and username.
     * Example: ['token' => '123abc', 'username' => 'john.doe.123@gmail.com'].
     *
     * {@inheritdoc}
     */
    public function verifyCredentials(array $credentials)
    {
        $token = $credentials['token'];
        $username = $credentials['username'];

        $result = ['is_error' => true, 'payload' => null];

        try {
            $payload = $this->google_client->verifyIdToken($token)->getAttributes()['payload'];

            if ($this->client_id !== $payload['aud']) {
                throw new RuntimeException('Unrecognized google_client.');
            }

            if (!in_array($payload['iss'], $this->getDomains())) {
                throw new RuntimeException('Wrong issuer.');
            }

            if ($username !== $payload['email']) {
                throw new RuntimeException('Email is not verified by Google.');
            }

            $result['is_error'] = false;
            $result['payload'] = $payload;
        } catch (Exception $e) {
            $result['payload'] = $e->getMessage();
        }

        return $result;
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
     * @return array
     */
    private function getDomains()
    {
        return ['accounts.google.com', 'https://accounts.google.com'];
    }
}
