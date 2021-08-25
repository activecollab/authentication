<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Apple;

use Azimo\Apple\Api\AppleApiClient;
use Azimo\Apple\Api\Factory\ResponseFactory;
use Azimo\Apple\Auth\Factory\AppleJwtStructFactory;
use Azimo\Apple\Auth\Jwt\JwtParser;
use Azimo\Apple\Auth\Jwt\JwtValidator;
use Azimo\Apple\Auth\Jwt\JwtVerifier;
use Azimo\Apple\Auth\Service\AppleJwtFetchingService;
use Azimo\Apple\Auth\Service\AppleJwtFetchingServiceInterface;
use Azimo\Apple\Auth\Struct\JwtPayload;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Validator;
use GuzzleHttp\Client;

class AppleClient implements AppleClientInterface
{
    private AppleJwtFetchingServiceInterface $service;

    public function __construct(string $client_id)
    {
        // Complicated? Nah, a lot easier to understand than a woman
        $this->service = new AppleJwtFetchingService(
            new JwtParser(new Parser(new JoseEncoder())),
            new JwtVerifier(
                new AppleApiClient(
                    new Client(
                        [
                            'base_uri'        => 'https://appleid.apple.com',
                            'timeout'         => 5,
                            'connect_timeout' => 5,
                        ]
                    ),
                    new ResponseFactory()
                ),
                new Validator(),
                new Sha256()
            ),
            new JwtValidator(
                new Validator(),
                [
                    new IssuedBy('https://appleid.apple.com'),
                    new PermittedFor($client_id),
                ]
            ),
            new AppleJwtStructFactory()
        );
    }

    public function getService(): AppleJwtFetchingService
    {
        return $this->service;
    }

    public function verifyIdToken(string $token): JwtPayload
    {
        return $this->service->getJwtPayload($token);
    }
}
