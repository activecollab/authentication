<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\Apple\AppleClientInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionHandler\ExceptionHandlerInterface;
use ActiveCollab\Authentication\Exception\RuntimeException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;
use Azimo\Apple\Auth\Struct\JwtPayload;

class AppleAuthorizer extends Authorizer
{
    use CredentialFieldsCheckTrait;
    use VerifyUserTrait;


    /**
     * @var RepositoryInterface
     */
    private $user_repository;

    /**
     * @var string
     */
    private $client_id;

    /**
     * @var array
     */
    private $user_profile;

    /**
     * @var $client
     */
    private $client;

    public function __construct(RepositoryInterface $user_repository, AppleClientInterface $client, $client_id, ExceptionHandlerInterface $exception_handler = null)
    {
        $this->user_repository = $user_repository;
        $this->client_id = $client_id;
        $this->user_profile = [];
        $this->client = $client;
        $this->setExceptionHandler($exception_handler);
    }


    /**
     * @throws UserNotFoundException
     * @param array $credentials
     * @return AuthenticatedUserInterface
     */
    public function verifyCredentials(array $credentials): AuthenticatedUserInterface
    {
        $this->verifyRequiredFields($credentials, ['token', 'username']);
        $token = $credentials['token'];
        $username = $credentials['username'];


        try {
            $payload = $this->client->verifyIdToken($token);

            $this->verifyAppleProfile($payload, $username);
            $user = $this->user_repository->findByUsername($payload->getEmail());
            $this->verifyUser($user);
            return $user;
        } catch (\Exception $exception) {
            throw new UserNotFoundException('User not found', 0, $exception);
        }
    }

    private function verifyAppleProfile(JwtPayload $payload, string $username)
    {
        if ($username !== $payload->getEmail() || !$payload->isEmailVerified()) {
            throw new RuntimeException('Email is not verified by Apple');
        }

        if (!in_array($this->client_id, $payload->getAud())) {
            throw new RuntimeException('Unrecognized client id');
        }

        if ($payload->getIss() !== "https://appleid.apple.com") {
            throw new RuntimeException('Wrong token issuer');
        }
    }
}
