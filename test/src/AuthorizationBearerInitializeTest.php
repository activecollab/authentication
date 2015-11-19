<?php

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\AuthorizationBearer;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\Base\RequestResponseTestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class AuthorizationBearerInitializeTest extends RequestResponseTestCase
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
     * Test if we can properly set header line using our stub request objects
     */
    public function testAuthorizationBearerTest()
    {
        $this->assertEquals('Bearer 123', $this->request->withHeader('Authorization', 'Bearer 123')->getHeaderLine('Authorization'));
    }

    /**
     * Test if adapter passes through when there's no authroization bearer header
     */
    public function testInitializationSkipWhenTheresNoAuthroizationHeader()
    {
        $this->assertNull((new AuthorizationBearer($this->empty_users_repository))->initialize($this->request));
    }

    /**
     * Test if adapter passes through when there's authroization bearer header, but it's not token bearer
     */
    public function testInitializationSkipWhenAuthorizationIsNotTokenBearer()
    {
        $this->assertNull((new AuthorizationBearer($this->empty_users_repository))->initialize($this->request->withHeader('Authorization', 'Basic 123')));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidTokenException
     */
    public function testExceptionWhenTokenIsNotValid()
    {
        (new AuthorizationBearer($this->empty_users_repository))->initialize($this->request->withHeader('Authorization', 'Bearer 123'));
    }

    /**
     * Test if we get authetncated user when we use a good token
     */
    public function testAuthenticationWithGoodToken()
    {
        $reposutory = new Repository([
            '123' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen'),
        ]);

        $user = (new AuthorizationBearer($reposutory))->initialize($this->request->withHeader('Authorization', 'Bearer 123'));

        $this->assertInstanceOf(AuthenticatedUser::class, $user);
    }

    /**
     * Test if authentication with good token records usage
     */
    public function testAuthenticationRecordsTokenUsage()
    {
        $reposutory = new Repository([
            '123' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen'),
        ]);

        $this->assertSame(0, $reposutory->getTokenUsage('123'));

        $user = (new AuthorizationBearer($reposutory))->initialize($this->request->withHeader('Authorization', 'Bearer 123'));
        $this->assertInstanceOf(AuthenticatedUser::class, $user);

        $this->assertSame(1, $reposutory->getTokenUsage('123'));
    }
}
