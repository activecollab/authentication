<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionHandler\ExceptionHandlerInterface;
use ActiveCollab\Authentication\Authorizer\RequestAware\RequestAware;
use ActiveCollab\Authentication\Authorizer\RequestAware\RequestAwareInterface;
use ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessorInterface;
use ActiveCollab\Authentication\Exception\InvalidCredentialsException;
use ActiveCollab\Authentication\Exception\UserNotFoundException;

/**
 * @package ActiveCollab\Authentication\Authorizer
 */
class SamlAuthorizer extends Authorizer implements RequestAwareInterface
{
    use RequestAware;

    /**
     * @var RepositoryInterface
     */
    private $user_repository;

    /**
     * @param RepositoryInterface       $user_repository
     * @param RequestProcessorInterface $request_processor
     * @param ExceptionHandlerInterface $exception_handler
     */
    public function __construct(RepositoryInterface $user_repository, RequestProcessorInterface $request_processor = null, ExceptionHandlerInterface $exception_handler = null)
    {
        $this->user_repository = $user_repository;
        $this->setRequestProcessor($request_processor);
        $this->setExceptionHandler($exception_handler);
    }

    /**
     * {@inheritdoc}
     */
    public function verifyCredentials(array $payload)
    {
        if (!isset($payload['username'])) {
            throw new InvalidCredentialsException();
        }

        $user = $this->user_repository->findByUsername($payload['username']);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
