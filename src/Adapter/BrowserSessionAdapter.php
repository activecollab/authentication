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
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransportInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authorization\AuthorizationTransportInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\CleanUp\CleanUpTransport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\CleanUp\CleanUpTransportInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Deauthentication\DeauthenticationTransport;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Deauthentication\DeauthenticationTransportInterface;
use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
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

                return new AuthenticationTransport($this, $user, $session);
            }
        }

        return new CleanUpTransport($this);
    }

    /**
     * {@inheritdoc}
     */
    public function applyTo(ServerRequestInterface $request, ResponseInterface $response, TransportInterface $transport)
    {
        // Extend session
        if ($transport instanceof AuthenticationTransportInterface) {
            $authenticated_with = $transport->getAuthenticatedWith();

            if (!$authenticated_with instanceof SessionInterface) {
                throw new InvalidArgumentException('Only user sessions are supported');
            }

            $authenticated_with->extendSession();

            list($request, $response) = $this->cookies->set($request, $response, $this->session_cookie_name, $authenticated_with->getSessionId(), [
                'ttl' => $authenticated_with->getSessionTtl(),
                'http_only' => true,
            ]);

        // Log in
        } elseif ($transport instanceof AuthorizationTransportInterface) {
            $authenticated_with = $transport->getAuthenticatedWith();

            if (!$authenticated_with instanceof SessionInterface) {
                throw new InvalidArgumentException('Only user sessions are supported');
            }

            list($request, $response) = $this->cookies->set($request, $response, $this->session_cookie_name, $authenticated_with->getSessionId(), [
                'ttl' => $authenticated_with->getSessionTtl(),
                'http_only' => true,
            ]);

        // Log out or clean-up
        } elseif ($transport instanceof DeauthenticationTransportInterface || $transport instanceof CleanUpTransportInterface) {
            list($request, $response) = $this->cookies->remove($request, $response, $this->session_cookie_name);
        }

        return parent::applyTo($request, $response, $transport);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(AuthenticatedUserInterface $authenticated_user, array $credentials = [])
    {
        return $this->session_repository->createSession($authenticated_user, $credentials);
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(AuthenticationResultInterface $authenticated_with)
    {
        if (!$authenticated_with instanceof SessionInterface) {
            throw new InvalidArgumentException('Instance is not a browser session');
        }

        $this->session_repository->terminateSession($authenticated_with);

        return new DeauthenticationTransport($this);
    }
}
