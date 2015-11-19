<?php

namespace ActiveCollab\Authentication\Test\Token;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Token\RepositoryInterface;
use ActiveCollab\Authentication\Token\TokenInterface;

/**
 * @package ActiveCollab\Authentication\Test\Token
 */
class Repository implements RepositoryInterface
{
    /**
     * @var array
     */
    private $prepared_tokens;

    /**
     * @param array $prepared_tokens
     */
    public function __construct(array $prepared_tokens = [])
    {
        $this->prepared_tokens = $prepared_tokens;
    }

    /**
     * Issue a new token
     *
     * @param  AuthenticatedUserInterface $user
     * @param  \DateTimeInterface|null    $expires_at
     * @return TokenInterface
     */
    public function issueToken(AuthenticatedUserInterface $user, \DateTimeInterface $expires_at = null)
    {
        $token = isset($this->prepared_tokens[$user->getEmail()]) ? $this->prepared_tokens[$user->getEmail()] : sha1(time());

        return new Token($token, $expires_at);
    }
}
