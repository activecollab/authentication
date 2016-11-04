<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Transport;
use ActiveCollab\Authentication\Exception\InvalidSessionException;
use ActiveCollab\Authentication\Session\RepositoryInterface as SessionRepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Cookies\CookiesInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\Adapter
 */
class BrowserSessionAdapter extends Adapter
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
    public function initialize(ServerRequestInterface $request)
    {
        $session_id = $this->cookies->get($request, $this->session_cookie_name);

        if (!$session_id) {
            return null;
        }

        $session = $this->session_repository->getById($session_id);

        if ($session instanceof SessionInterface) {
            if ($user = $session->getAuthenticatedUser($this->user_repository)) {
                $this->session_repository->recordUsageBySession($session);

                return new Transport($this, $user, $session);
            }
        }

        throw new InvalidSessionException();
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(ServerRequestInterface $request, ResponseInterface $response, AuthenticatedUserInterface $authenticated_user, AuthenticationResultInterface $authenticated_with, array $additional_arguments)
    {
        if (!$authenticated_with instanceof SessionInterface) {
            throw new InvalidArgumentException('Only user sessions are supported');
        }

        list ($request, $response) = $this->cookies->set($request, $response, $this->session_cookie_name, $authenticated_with->getSessionId(), [
            'ttl' => $authenticated_with->getSessionTtl(),
        ]);

        return parent::finalize($request, $response, $authenticated_user, $authenticated_with, $additional_arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(AuthenticatedUserInterface $authenticated_user)
    {
        return $this->session_repository->createSession($authenticated_user);
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
