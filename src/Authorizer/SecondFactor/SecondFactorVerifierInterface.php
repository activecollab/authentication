<?php

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer\SecondFactor;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;

interface SecondFactorVerifierInterface
{
    public function verifySecondFactorCode(
        string $secondFactorCode,
        AuthenticatedUserInterface $user,
    ): bool;
}
