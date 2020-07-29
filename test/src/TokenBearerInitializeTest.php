<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\TokenBearerAdapter;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Authentication\Exception\InvalidTokenException;
use ActiveCollab\Authentication\Test\AuthenticatedUser\AuthenticatedUser;
use ActiveCollab\Authentication\Test\AuthenticatedUser\Repository as UserRepository;
use ActiveCollab\Authentication\Test\TestCase\TokenBearerTestCase;
use ActiveCollab\Authentication\Test\Token\Repository as TokenRepository;
use ActiveCollab\Authentication\Test\Token\Token;

class TokenBearerInitializeTest extends TokenBearerTestCase
{
    /**
     * Test if we can properly set header line using our stub request objects.
     */
    public function testAuthorizationBearerHeaderLine()
    {
        $this->assertEquals('Bearer 123', $this->request->withHeader('Authorization', 'Bearer 123')->getHeaderLine('Authorization'));
    }

    /**
     * Test if adapter passes through when there's no authroization bearer header.
     */
    public function testInitializationSkipWhenTheresNoAuthroizationHeader()
    {
        $this->assertNull((new TokenBearerAdapter($this->empty_user_repository, $this->empty_token_repository))->initialize($this->request));
    }

    /**
     * Test if adapter passes through when there's authroization bearer header, but it's not token bearer.
     */
    public function testInitializationSkipWhenAuthorizationIsNotTokenBearer()
    {
        $this->assertNull((new TokenBearerAdapter($this->empty_user_repository, $this->empty_token_repository))->initialize($this->request->withHeader('Authorization', 'Basic 123')));
    }

    public function testExceptionWhenTokenIsNotSet()
    {
        $this->expectException(InvalidTokenException::class);

        (new TokenBearerAdapter($this->empty_user_repository, $this->empty_token_repository))->initialize($this->request->withHeader('Authorization', 'Bearer'));
    }

    public function testExceptionWhenTokenIsNotValid()
    {
        $this->expectException(InvalidTokenException::class);

        (new TokenBearerAdapter($this->empty_user_repository, $this->empty_token_repository))->initialize($this->request->withHeader('Authorization', 'Bearer 123'));
    }

    /**
     * Test if we get authenticated user when we use a good token.
     */
    public function testAuthenticationWithGoodToken()
    {
        $test_token = '123';

        $user_repository = new UserRepository([new AuthenticatedUser(1, 'ilija.studen@activecollab.com', 'Ilija Studen', '123')]);
        $token_repository = new TokenRepository([$test_token => new Token($test_token, 'ilija.studen@activecollab.com')]);

        $results = (new TokenBearerAdapter($user_repository, $token_repository))->initialize($this->request->withHeader('Authorization', "Bearer {$test_token}"));
        $this->assertInstanceOf(TransportInterface::class, $results);

        $this->assertInstanceOf(AuthenticatedUser::class, $results->getAuthenticatedUser());
        $this->assertInstanceOf(Token::class, $results->getAuthenticatedWith());
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

        $results = (new TokenBearerAdapter($user_repository, $token_repository))->initialize($this->request->withHeader('Authorization', "Bearer {$test_token}"));

        $this->assertInstanceOf(TransportInterface::class, $results);

        $this->assertInstanceOf(AuthenticatedUser::class, $results->getAuthenticatedUser());
        $this->assertInstanceOf(Token::class, $results->getAuthenticatedWith());

        $this->assertSame(1, $token_repository->getUsageById($test_token));
    }
}
