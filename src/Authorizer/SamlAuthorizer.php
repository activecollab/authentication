<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Exception\UserNotFoundException;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Protocol\Response;

/**
 * @package ActiveCollab\Authentication\Authorizer
 */
class SamlAuthorizer implements AuthorizerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $user_repository;

    /**
     * @param RepositoryInterface $user_repository
     */
    public function __construct(RepositoryInterface $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    /**
     * {@inheritdoc}
     */
    public function verifyCredentials(array $payload)
    {
        $deserializationContext = new DeserializationContext();
        $deserializationContext->getDocument()->loadXML(base64_decode($payload['SAMLResponse']));

        $saml_response = new Response();
        $saml_response->deserialize($deserializationContext->getDocument()->firstChild, $deserializationContext);

        $username = null;

        foreach ($saml_response->getAllAssertions() as $assertion) {
            if ($assertion->getSubject()) {
                $username = $assertion->getSubject()->getNameID();
            }
        }

        $user = $this->user_repository->findByUsername($username->getValue());

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogin(array $payload)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onLogout(array $payload)
    {
    }
}
