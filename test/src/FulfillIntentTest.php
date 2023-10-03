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
use ActiveCollab\Authentication\AuthenticationInterface;
use ActiveCollab\Authentication\Authorizer\AuthorizerInterface;
use ActiveCollab\Authentication\Exception\IntentExpiredException;
use ActiveCollab\Authentication\Exception\IntentFulfilledException;
use ActiveCollab\Authentication\Exception\InvalidIntentUserException;
use ActiveCollab\Authentication\Exception\SecondFactorNotRequiredException;
use ActiveCollab\Authentication\Intent\IntentInterface;
use ActiveCollab\Authentication\Intent\RepositoryInterface as IntentRepositoryInterface;
use ActiveCollab\Authentication\Test\TestCase\TestCase;

class FulfillIntentTest extends TestCase
{
    public function testWillRejectFulfilledIntent(): void
    {
        $this->expectException(IntentFulfilledException::class);

        $intent = $this->createMock(IntentInterface::class);
        $intent
            ->expects($this->once())
            ->method('isFulfilled')
            ->willReturn(true);

        $this->createAuthentication()->fulfillIntent(
            $this->createMock(AuthorizerInterface::class),
            $this->createMock(AdapterInterface::class),
            $intent,
            $this->createMock(AuthenticatedUserInterface::class),
            [],
        );
    }

    public function testWillRejectExpiredIntent(): void
    {
        $this->expectException(IntentExpiredException::class);

        $intent = $this->createMock(IntentInterface::class);
        $intent
            ->expects($this->once())
            ->method('isFulfilled')
            ->willReturn(false);

        $intent
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(true);

        $this->createAuthentication()->fulfillIntent(
            $this->createMock(AuthorizerInterface::class),
            $this->createMock(AdapterInterface::class),
            $intent,
            $this->createMock(AuthenticatedUserInterface::class),
            [],
        );
    }

    public function testWillRejectIntentForDifferentUser(): void
    {
        $this->expectException(InvalidIntentUserException::class);

        $intent = $this->createMock(IntentInterface::class);
        $intent
            ->expects($this->once())
            ->method('isFulfilled')
            ->willReturn(false);

        $intent
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(false);

        $user = $this->createMock(AuthenticatedUserInterface::class);

        $intent
            ->expects($this->once())
            ->method('belongsToUser')
            ->with($user)
            ->willReturn(false);

        $this->createAuthentication()->fulfillIntent(
            $this->createMock(AuthorizerInterface::class),
            $this->createMock(AdapterInterface::class),
            $intent,
            $user,
            [],
        );
    }

    public function testWillRejectIntentWhenNotRequired(): void
    {
        $this->expectException(SecondFactorNotRequiredException::class);

        $intent = $this->createMock(IntentInterface::class);
        $intent
            ->expects($this->once())
            ->method('isFulfilled')
            ->willReturn(false);

        $intent
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(false);

        $user = $this->createMock(AuthenticatedUserInterface::class);
        $user
            ->expects($this->once())
            ->method('requiresSecondFactor')
            ->willReturn(false);

        $intent
            ->expects($this->once())
            ->method('belongsToUser')
            ->with($user)
            ->willReturn(true);

        $this->createAuthentication()->fulfillIntent(
            $this->createMock(AuthorizerInterface::class),
            $this->createMock(AdapterInterface::class),
            $intent,
            $user,
            [],
        );
    }

    public function testWillFulfillIntent(): void
    {
        $intent = $this->createMock(IntentInterface::class);
        $intent
            ->expects($this->once())
            ->method('isFulfilled')
            ->willReturn(false);

        $intent
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(false);

        $user = $this->createMock(AuthenticatedUserInterface::class);
        $user
            ->expects($this->once())
            ->method('requiresSecondFactor')
            ->willReturn(true);

        $intent
            ->expects($this->once())
            ->method('belongsToUser')
            ->with($user)
            ->willReturn(true);

        $credentials = [
            'one' => 1,
            'two' => 2,
            'three' => 3,
        ];

        $intent
            ->expects($this->once())
            ->method('fulfill')
            ->with(
                $user,
                $credentials,
            );

        $authentication = $this->createAuthentication();

        $callback_called = false;
        $authentication->onIntentFulfilled(function () use (&$callback_called) {
            $callback_called = true;
        });

        $authentication->fulfillIntent(
            $this->createMock(AuthorizerInterface::class),
            $this->createMock(AdapterInterface::class),
            $intent,
            $user,
            $credentials,
        );

        $this->assertTrue($callback_called);
    }

    private function createAuthentication(): AuthenticationInterface
    {
        return new Authentication(
            $this->createMock(IntentRepositoryInterface::class),
        );
    }
}