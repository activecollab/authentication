<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearer;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\TestCase\TokenBearerTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;
use ActiveCollab\Authentication\Token\TokenInterface;

/**
 * @package ActiveCollab\Authentication\Test
 */
class TokenBearerInitializeTest extends TokenBearerTestCase
{
    /**
     * Test if we can properly set header line using our stub request objects.
     */
    public function testAuthorizationBearerTest()
    {
        $this->assertEquals('Bearer 123', $this->request->withHeader('Authorization', 'Bearer 123')->getHeaderLine('Authorization'));
    }

    /**
     * Test if adapter passes through when there's no authroization bearer header.
     */
    public function testInitializationSkipWhenTheresNoAuthroizationHeader()
    {
        $this->assertNull((new TokenBearer($this->empty_user_repository, $this->empty_token_repository))->initialize($this->request));
    }

    /**
     * Test if adapter passes through when there's authroization bearer header, but it's not token bearer.
     */
    public function testInitializationSkipWhenAuthorizationIsNotTokenBearer()
    {
        $this->assertNull((new TokenBearer($this->empty_user_repository, $this->empty_token_repository))->initialize($this->request->withHeader('Authorization', 'Basic 123')));
    }

    /**
     * @expectedException \ActiveCollab\Authentication\Exception\InvalidTokenException
     */
    public function testExceptionWhenTokenIsNotValid()
    {
        (new TokenBearer($this->empty_user_repository, $this->empty_token_repository))->initialize($this->request->withHeader('Authorization', 'Bearer 123'));
    }

    /**
     * Test if we get authenticated user when we use a good token.
     */
    public function testAuthenticationWithGoodToken()
    {
        $test_token = '123';

        $user_repository = new UserRepository([new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123')]);
        $token_repository = new TokenRepository([$test_token => new Token($test_token, 'ilija.studen@activecollab.com')]);

        $user = (new TokenBearer($user_repository, $token_repository))->initialize($this->request->withHeader('Authorization', "Bearer {$test_token}"));

        $this->assertInstanceOf(AuthenticatedUser::class, $user);
    }

    /**
     * Test if we get authenticated user when we use a good token.
     */
    public function testAuthenticationWithGoodTokenAlsoSetsToken()
    {
        $test_token = '123';

        $user_repository = new UserRepository([new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123')]);
        $token_repository = new TokenRepository([$test_token => new Token($test_token, 'ilija.studen@activecollab.com')]);

        $token = null;

        $user = (new TokenBearer($user_repository, $token_repository))->initialize($this->request->withHeader('Authorization', "Bearer {$test_token}"), $token);

        $this->assertInstanceOf(AuthenticatedUser::class, $user);
        $this->assertInstanceOf(TokenInterface::class, $token);
    }

    /**
     * Test if authentication with good token records usage.
     */
    public function testAuthenticationRecordsTokenUsage()
    {
        $test_token = '123';

        $user_repository = new UserRepository([new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123')]);
        $token_repository = new TokenRepository([$test_token => new Token($test_token, 'ilija.studen@activecollab.com')]);

        $this->assertSame(0, $token_repository->getUsageById($test_token));

        $user = (new TokenBearer($user_repository, $token_repository))->initialize($this->request->withHeader('Authorization', "Bearer {$test_token}"));
        $this->assertInstanceOf(AuthenticatedUser::class, $user);

        $this->assertSame(1, $token_repository->getUsageById($test_token));
    }
}
