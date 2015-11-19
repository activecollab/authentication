<?php

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearer;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository;
use ActiveCollab\Authentication\Test\Base\AuthorizationBearerTestCase;

/**
 * @package ActiveCollab\Authentication\Test
 */
class TokenBearerInitializeTest extends AuthorizationBearerTestCase
{
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
        $this->assertNull((new TokenBearer($this->empty_users_repository, $this->empty_tokens_repository))->initialize($this->request));
    }

    /**
     * Test if adapter passes through when there's authroization bearer header, but it's not token bearer
     */
    public function testInitializationSkipWhenAuthorizationIsNotTokenBearer()
    {
        $this->assertNull((new TokenBearer($this->empty_users_repository, $this->empty_tokens_repository))->initialize($this->request->withHeader('Authorization', 'Basic 123')));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidToken
     */
    public function testExceptionWhenTokenIsNotValid()
    {
        (new TokenBearer($this->empty_users_repository, $this->empty_tokens_repository))->initialize($this->request->withHeader('Authorization', 'Bearer 123'));
    }

    /**
     * Test if we get authetncated user when we use a good token
     */
    public function testAuthenticationWithGoodToken()
    {
        $repository = new Repository([], [
            '123' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        $user = (new TokenBearer($repository, $this->empty_tokens_repository))->initialize($this->request->withHeader('Authorization', 'Bearer 123'));

        $this->assertInstanceOf(AuthenticatedUser::class, $user);
    }

    /**
     * Test if authentication with good token records usage
     */
    public function testAuthenticationRecordsTokenUsage()
    {
        $repository = new Repository([], [
            '123' => new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123'),
        ]);

        $this->assertSame(0, $repository->getTokenUsage('123'));

        $user = (new TokenBearer($repository, $this->empty_tokens_repository))->initialize($this->request->withHeader('Authorization', 'Bearer 123'));
        $this->assertInstanceOf(AuthenticatedUser::class, $user);

        $this->assertSame(1, $repository->getTokenUsage('123'));
    }
}
