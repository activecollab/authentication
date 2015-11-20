<?php

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\BrowserSession;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\Base\BrowserSessionTestCase;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7;

/**
 * @package ActiveCollab\Authentication\Test
 */
class BrowserSessionAuthenticateTest extends BrowserSessionTestCase
{
    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidAuthenticateRequest
     */
    public function testInvalidRequestThrowsAnException()
    {
        (new BrowserSession($this->empty_users_repository, $this->empty_sessions_repository))->authenticate($this->prepareAuthorizationRequest('', ''));
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
