<?php


namespace ActiveCollab\Authentication\Authorizer;


use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Exception\UserNotFoundException;

trait VerifyUserTrait
{
    /**
     * @param AuthenticatedUserInterface|null $user
     */
    private function verifyUser(AuthenticatedUserInterface $user = null)
    {
        if (!$user || !$user->canAuthenticate()) {
            throw new UserNotFoundException();
        }
    }
}
