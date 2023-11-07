<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Authentication;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authorization\AuthorizationTransportInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Authentication\Test\TestCase\TestCase;
use ActiveCollab\Authentication\Intent\RepositoryInterface as IntentRepositoryInterface;

class AuthorizeTest extends TestCase
{
    public function testWillAuthorizeWithValidCredentials(): void
    {
        $credentials = [
            'username' => 'user',
            'password' => 'pass',
        ];

        $user = $this->createMock(AuthenticatedUserInterface::class);

        $authorizer = $this->createMock(AuthorizerInterface::class);
        $authorizer
            ->expects($this->once())
            ->method('verifyCredentials')
            ->with($credentials)
            ->willReturn($user);

        $session = $this->createMock(SessionInterface::class);

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn($session);

        $transport = (new Authentication(
            $this->createMock(IntentRepositoryInterface::class)
        ))->authorize(
            $authorizer,
            $adapter,
            $credentials,
        );

        $this->assertInstanceOf(
            AuthorizationTransportInterface::class,
            $transport,
        );

        $this->assertInstanceOf(AuthenticatedUserInterface::class, $transport->getAuthenticatedUser());
        $this->assertInstanceOf(SessionInterface::class, $transport->getAuthenticatedWith());
    }

    public function testWillIssueIntentWithValidCredentials(): void
    {
        $this->markTestSkipped();
    }

    public function testWillNotIssueIntentIfAuthorizerDoesNotSupportSecondFactor(): void
    {
        $this->markTestSkipped();
    }

    public function testWillNotIssueIntentIfUserDoesNotRequireSecondFactor(): void
    {
        $this->markTestSkipped();
    }
}
