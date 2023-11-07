<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionHandler\ExceptionHandlerInterface;
use ActiveCollab\Authentication\Authorizer\RequestAware\RequestAware;
use ActiveCollab\Authentication\Authorizer\RequestAware\RequestAwareInterface;
use ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessorInterface;
use ActiveCollab\Authentication\Exception\InvalidCredentialsException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;

class SamlAuthorizer extends Authorizer implements RequestAwareInterface
{
    use RequestAware;

    public function __construct(
        private RepositoryInterface $user_repository,
        RequestProcessorInterface $request_processor = null,
        ExceptionHandlerInterface $exception_handler = null,
        private bool $supports_second_factor = true,
    )
    {
        parent::__construct($supports_second_factor);

        $this->setRequestProcessor($request_processor);
        $this->setExceptionHandler($exception_handler);
    }

    public function verifyCredentials(array $credentials): ?AuthenticatedUserInterface
    {
        if (!isset($credentials['username'])) {
            throw new InvalidCredentialsException();
        }

        $user = $this->user_repository->findByUsername($credentials['username']);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
