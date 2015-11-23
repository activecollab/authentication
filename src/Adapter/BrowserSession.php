<?php

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\AuthenticationResultInterface;
use ActiveCollab\Authentication\Exception\InvalidSession;
use ActiveCollab\Authentication\Session\RepositoryInterface as SessionRepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
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
    private $users_repository;

    /**
     * @var SessionRepositoryInterface
     */
    private $sessions_repository;

    /**
     * @var string
     */
    private $session_cookie_name;

    /**
     * @param UserRepositoryInterface    $users_repository
     * @param SessionRepositoryInterface $sessions_repository
     * @param string                     $session_cookie_name
     */
    public function __construct(UserRepositoryInterface $users_repository, SessionRepositoryInterface $sessions_repository, $session_cookie_name = 'sessid')
    {
        if (empty($session_cookie_name)) {
            throw new InvalidArgumentException('Session cookie name is required');
        }

        $this->users_repository = $users_repository;
        $this->sessions_repository = $sessions_repository;
        $this->session_cookie_name = $session_cookie_name;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ServerRequestInterface $request, &$authenticated_with = null)
    {
        $cookie_params = $request->getCookieParams();

        if (!empty($cookie_params[$this->session_cookie_name])) {
            $session_id = $cookie_params[$this->session_cookie_name];

            $session = $this->sessions_repository->getById($session_id);

            if ($session instanceof SessionInterface) {
                if ($user = $session->getAuthenticatedUser($this->users_repository)) {
                    $this->sessions_repository->recordUsage($session);
                    $authenticated_with = $session;

                    return $user;
                }
            }

            throw new InvalidSession();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(ServerRequestInterface $request)
    {
        return $this->sessions_repository->createSession($this->getUserFromCredentials($this->users_repository, $this->getAuthenticationCredentialsFromRequest($request)));
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(AuthenticationResultInterface $authenticated_with)
    {
        if ($authenticated_with instanceof SessionInterface) {
            $this->sessions_repository->terminateSession($authenticated_with);
        } else {
            throw new InvalidArgumentException('Instance is not a browser session');
        }
    }
}