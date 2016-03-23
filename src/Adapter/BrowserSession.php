<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\Exception\InvalidSessionException;
use ActiveCollab\Authentication\Session\RepositoryInterface as SessionRepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Cookies\CookiesInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
class BrowserSession extends Adapter
{
    /**
     * @var UserRepositoryInterface
     */
    private $user_repository;

    /**
     * @var SessionRepositoryInterface
     */
    private $session_repository;

    /**
     * @var CookiesInterface
     */
    private $cookies;

    /**
     * @var string
     */
    private $session_cookie_name;

    /**
     * @param UserRepositoryInterface    $user_repository
     * @param SessionRepositoryInterface $session_repository
     * @param CookiesInterface           $cookies
     * @param string                     $session_cookie_name
     */
    public function __construct(UserRepositoryInterface $user_repository, SessionRepositoryInterface $session_repository, CookiesInterface $cookies, $session_cookie_name = 'sessid')
    {
        if (empty($session_cookie_name)) {
            throw new InvalidArgumentException('Session cookie name is required');
        }

        $this->user_repository = $user_repository;
        $this->session_repository = $session_repository;
        $this->cookies = $cookies;
        $this->session_cookie_name = $session_cookie_name;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ServerRequestInterface $request, &$authenticated_with = null)
    {
        $session_id = $this->cookies->get($request, $this->session_cookie_name);

        if (!$session_id) {
            return null;
        }

        $session = $this->session_repository->getById($session_id);

        if ($session instanceof SessionInterface) {
            if ($user = $session->getAuthenticatedUser($this->user_repository)) {
                $this->session_repository->recordUsageBySession($session);
                $authenticated_with = $session;

                return $user;
            }
        }

        throw new InvalidSessionException();
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(ServerRequestInterface $request, $check_password = true)
    {
        return $this->session_repository->createSession(
            $this->getUserFromCredentials(
                $this->user_repository,
                $this->getAuthenticationCredentialsFromRequest($request, $check_password),
                $check_password
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(AuthenticationResultInterface $authenticated_with)
    {
        if ($authenticated_with instanceof SessionInterface) {
            $this->session_repository->terminateSession($authenticated_with);
        } else {
            throw new InvalidArgumentException('Instance is not a browser session');
        }
    }
}
