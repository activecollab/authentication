<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

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
use ActiveCollab\Cookies\Adapter\CookieSetterInterface;
use ActiveCollab\Cookies\CookiesInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BrowserSessionAdapter extends Adapter implements BrowserSessionAdapterInterface
{
    private $user_repository;
    private $session_repository;
    private $cookies;
    private $session_cookie_name;

    public function __construct(
        UserRepositoryInterface $user_repository,
        SessionRepositoryInterface $session_repository,
        CookiesInterface $cookies,
        string $session_cookie_name = 'sessid'
    )
    {
        if (empty($session_cookie_name)) {
            throw new InvalidArgumentException('Session cookie name is required');
        }

        $this->user_repository = $user_repository;
        $this->session_repository = $session_repository;
        $this->cookies = $cookies;
        $this->session_cookie_name = $session_cookie_name;
    }

    public function initialize(ServerRequestInterface $request): ?TransportInterface
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

    public function applyToResponse(ResponseInterface $response, TransportInterface $transport): ResponseInterface
    {
        // Extend session
        if ($transport instanceof AuthenticationTransportInterface) {
            $authenticated_with = $transport->getAuthenticatedWith();

            if (!$authenticated_with instanceof SessionInterface) {
                throw new InvalidArgumentException('Only user sessions are supported');
            }

            $authenticated_with->extendSession();

            /** @var CookieSetterInterface $cookieSetter */
            $cookieSetter = $this->cookies->createSetter(
                $this->session_cookie_name,
                $authenticated_with->getSessionId(),
                [
                    'ttl' => $authenticated_with->getSessionTtl(),
                    'http_only' => true,
                ]
            );

            $response = $cookieSetter->applyToResponse($response);

        // Log in
        } elseif ($transport instanceof AuthorizationTransportInterface) {
            $authenticated_with = $transport->getAuthenticatedWith();

            if (!$authenticated_with instanceof SessionInterface) {
                throw new InvalidArgumentException('Only user sessions are supported');
            }

            /** @var CookieSetterInterface $cookieSetter */
            $cookieSetter = $this->cookies->createSetter(
                $this->session_cookie_name,
                $authenticated_with->getSessionId(),
                [
                    'ttl' => $authenticated_with->getSessionTtl(),
                    'http_only' => true,
                ]
            );

            return $cookieSetter->applyToResponse($response);

        // Log out or clean-up
        } elseif ($transport instanceof DeauthenticationTransportInterface
            || $transport instanceof CleanUpTransportInterface
        ) {
            return $this->cookies
                ->createRemover($this->session_cookie_name)
                    ->applyToResponse($response);
        }

        return $response;
    }

    public function authenticate(
        AuthenticatedUserInterface $authenticated_user,
        array $credentials = []
    ): AuthenticationResultInterface
    {
        return $this->session_repository->createSession($authenticated_user, $credentials);
    }

    public function terminate(AuthenticationResultInterface $authenticated_with): TransportInterface
    {
        if (!$authenticated_with instanceof SessionInterface) {
            throw new InvalidArgumentException('Instance is not a browser session');
        }

        $this->session_repository->terminateSession($authenticated_with);

        return new DeauthenticationTransport($this);
    }
}
