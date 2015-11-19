<?php

namespace ActiveCollab\Authentication\Token;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;

/**
 * @package ActiveCollab\Authentication\Token
 */
interface RepositoryInterface
{
    /**
     * Issue a new token
     *
     * @param  AuthenticatedUserInterface $user
     * @param  \DateTimeInterface|null    $expires_at
     * @return TokenInterface
     */
    public function issueToken(AuthenticatedUserInterface $user, \DateTimeInterface $expires_at = null);
}
