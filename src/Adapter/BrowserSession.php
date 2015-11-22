<?php

namespace ActiveCollab\Authentication\Adapter;

use ActiveCollab\Authentication\AuthenticationResultInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface as UserRepositoryInterface;
use ActiveCollab\Authentication\Session\RepositoryInterface as SessionRepositoryInterface;
use ActiveCollab\Authentication\Exception\InvalidSession;
use ActiveCollab\Authentication\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;
use InvalidArgumentException;

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
                if ($user = $this->users_repository->findBySessionId($session_id)) {
                    $this->sessions_repository->recordSessionUsage($session_id);
                    $authenticated_with = $session;

                    return $user;
                }
            }

            throw new InvalidSession();
        }

        return null;
    }

    /**
     * Authenticate with given credential agains authentication source
     *
     * @param  ServerRequestInterface        $request
     * @return AuthenticationResultInterface
     */
    public function authenticate(ServerRequestInterface $request)
    {
        return $this->sessions_repository->createSession($this->getUserFromCredentials($this->users_repository, $this->getAuthenticationCredentialsFromRequest($request)));
    }
}
