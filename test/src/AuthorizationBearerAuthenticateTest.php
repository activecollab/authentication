<?php

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\AuthorizationBearer;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
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

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidAuthenticateRequest
     */
    public function testInvalidRequestThrowsAnException()
    {
        (new AuthorizationBearer($this->empty_users_repository))->authenticate($this->prepareAuthorizationRequest('', ''));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\UserNotFound
     */
    public function testUserNotFoundThrowsAnException()
    {
        (new AuthorizationBearer($this->empty_users_repository))->authenticate($this->prepareAuthorizationRequest('not found', '123'));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidPassword
     */
    public function testInvalidPasswordThrowsAnException()
    {
        $repository = new Repository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        (new AuthorizationBearer($repository))->authenticate($this->prepareAuthorizationRequest('ilija.studen@activecollab.com', 'not 123'));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\UserNotFound
     */
    public function testUserCantAuthenticateThrowsAnException()
    {
        $repository = new Repository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123', false),
        ]);

        (new AuthorizationBearer($repository))->authenticate($this->prepareAuthorizationRequest('ilija.studen@activecollab.com', '123'));
    }

    /**
     * Test if good credentials authenticate the user
     */
    public function testGoodCredentialsAuthenticateUser()
    {
        $repository = new Repository([
            'ilija.studen@activecollab.com' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        $result = (new AuthorizationBearer($repository))->authenticate($this->prepareAuthorizationRequest('ilija.studen@activecollab.com', '123'));

        $this->assertNotEmpty($result);
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
