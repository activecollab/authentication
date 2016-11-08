<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Saml;

use DateTime;
use LightSaml\Binding\BindingFactory;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Helper;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\SamlConstants;

/**
 * @package ActiveCollab\Authentication\Saml
 */
class SamlUtils
{
    /**
     * @var string
     */
    private $consumer_service_url;

    /**
     * @var string
     */
    private $idp_destination;

    /**
     * @var string
     */
    private $issuer;

    /**
     * @var string
     */
    private $saml_crt;

    /**
     * @var string
     */
    private $saml_key;

    /**
     * @param string $consumer_service_url
     * @param string $idp_destination
     * @param string $issuer
     * @param string $saml_crt
     * @param string $saml_key
     */
    public function __construct(
        $consumer_service_url,
        $idp_destination,
        $issuer,
        $saml_crt,
        $saml_key
    ) {
        $this->consumer_service_url = $consumer_service_url;
        $this->idp_destination = $idp_destination;
        $this->issuer = $issuer;
        $this->saml_crt = $saml_crt;
        $this->saml_key = $saml_key;
    }

    /**
     * Get saml authnRequest.
     *
     * @return string
     */
    public function getAuthnRequest()
    {
        $authn_request = new AuthnRequest();
        $authn_request
            ->setAssertionConsumerServiceURL($this->consumer_service_url)
            ->setProtocolBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
            ->setID(Helper::generateID())
            ->setIssueInstant(new DateTime())
            ->setDestination($this->idp_destination)
            ->setIssuer(new Issuer($this->issuer));

        $certificate = new X509Certificate();
        $certificate->loadPem($this->saml_crt);
        $private_key = KeyHelper::createPrivateKey($this->saml_key, '', false);

        $authn_request->setSignature(new SignatureWriter($certificate, $private_key));

        $serialization_context = new SerializationContext();
        $authn_request->serialize($serialization_context->getDocument(), $serialization_context);

        $binding_factory = new BindingFactory();
        $redirect_binding = $binding_factory->create(SamlConstants::BINDING_SAML2_HTTP_REDIRECT);

        $message_context = new MessageContext();
        $message_context->setMessage($authn_request);

        /** @var \Symfony\Component\HttpFoundation\RedirectResponse $http_response */
        $http_response = $redirect_binding->send($message_context);

        return $http_response->getTargetUrl();
    }
}
