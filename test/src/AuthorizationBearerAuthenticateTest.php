<?php

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\AuthorizationBearer;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\Base\RequestResponseTestCase;
use GuzzleHttp\Psr7;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Test
 */
class AuthorizationBearerAuthenticateTest extends RequestResponseTestCase
{
    /**
     * @var RepositoryInterface
     */
    private $empty_users_repository;

    /**
     * Set up test environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->empty_users_repository = new Repository();
    }

    public function testUserNotFoundThrowsAnException()
    {
        (new AuthorizationBearer($this->empty_users_repository))->authenticate($this->prepareAuthorizationRequest('not found', '123'));
    }

    public function testInvalidPasswordThrowsAnException()
    {

    }

    public function testUserCantAuthenticateThrowsAnException()
    {

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
