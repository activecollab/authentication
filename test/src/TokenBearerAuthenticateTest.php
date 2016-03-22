<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearer;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\Base\TokenBearerTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Token\TokenInterface;
use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Test
 */
class TokenBearerAuthenticateTest extends TokenBearerTestCase
{
    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidAuthenticateRequest
     */
    public function testInvalidRequestThrowsAnException()
    {
        (new TokenBearer($this->empty_users_repository, $this->empty_tokens_repository))->authenticate($this->prepareAuthorizationRequest('', ''));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\UserNotFound
     */
    public function testUserNotFoundThrowsAnException()
    {
        (new TokenBearer($this->empty_users_repository, $this->empty_tokens_repository))->authenticate($this->prepareAuthorizationRequest('not found', '123'));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPassword
     */
    public function testInvalidPasswordThrowsAnException()
    {
        $repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        (new TokenBearer($repository, $this->empty_tokens_repository))->authenticate($this->prepareAuthorizationRequest('ilija.studen@activecollab.com', 'not 123'));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\UserNotFound
     */
    public function testUserCantAuthenticateThrowsAnException()
    {
        $repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123', false),
        ]);

        (new TokenBearer($repository, $this->empty_tokens_repository))->authenticate($this->prepareAuthorizationRequest('ilija.studen@activecollab.com', '123'));
    }

    /**
     * Test if good credentials authenticate the user.
     */
    public function testGoodCredentialsAuthenticateUser()
    {
        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        $result = (new TokenBearer($user_repository, $this->empty_tokens_repository))->authenticate($this->prepareAuthorizationRequest('ilija.studen@activecollab.com', '123'));

        $this->assertInstanceOf(AuthenticationResultInterface::class, $result);
        $this->assertInstanceOf(TokenInterface::class, $result);
    }

    /**
     * Test if authentication result can be converted to a valid JSON response.
     */
    public function testAuthenticationResultToResponse()
    {
        $user_repository = new UserRepository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        $token_repository = new TokenRepository([
            'ilija.studen@activecollab.com' => 'awesome-token',
        ]);

        $result = (new TokenBearer($user_repository, $token_repository))->authenticate($this->prepareAuthorizationRequest('ilija.studen@activecollab.com', '123'));

        $this->assertInstanceOf(AuthenticationResultInterface::class, $result);
        $this->assertInstanceOf(TokenInterface::class, $result);

        /** @var ResponseInterface $response */
        $response = $result->toResponse($this->response);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        $response_body = (string) $response->getBody();

        $this->assertNotEmpty($response_body);

        $decoded_response_body = json_decode($response_body, true);

        $this->assertInternalType('array', $decoded_response_body);
        $this->assertCount(3, $decoded_response_body);
        $this->assertEquals('awesome-token', $decoded_response_body['token']);
        $this->assertEquals('ilija.studen@activecollab.com', $decoded_response_body['user_id']);
        $this->assertNull($decoded_response_body['expires_at']);
    }

    /**
     * @param  string                 $username
     * @param  string                 $password
     * @return ServerRequestInterface
     */
    private function prepareAuthorizationRequest($username, $password)
    {
        return $this->request->withHeader('Content-Type', 'application/json')->withBody(Psr7\stream_for(json_encode([
            'username' => $username,
            'password' => $password,
        ])));
    }
}
