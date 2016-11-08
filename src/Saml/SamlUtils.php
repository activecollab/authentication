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
        string $consumer_service_url,
        string $idp_destination,
        string $issuer,
        string $saml_crt,
        string $saml_key
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
        $authnRequest = new AuthnRequest();
        $authnRequest
            ->setAssertionConsumerServiceURL($this->consumer_service_url)
            ->setProtocolBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
            ->setID(Helper::generateID())
            ->setIssueInstant(new DateTime())
            ->setDestination($this->idp_destination)
            ->setIssuer(new Issuer($this->issuer));

        $certificate = new X509Certificate();
        $certificate->loadPem($this->saml_crt);
        $privateKey = KeyHelper::createPrivateKey($this->saml_key, '', false);

        $authnRequest->setSignature(new SignatureWriter($certificate, $privateKey));

        $serializationContext = new SerializationContext();
        $authnRequest->serialize($serializationContext->getDocument(), $serializationContext);

        $bindingFactory = new BindingFactory();
        $redirectBinding = $bindingFactory->create(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT);

        $messageContext = new MessageContext();
        $messageContext->setMessage($authnRequest);

        /** @var \Symfony\Component\HttpFoundation\RedirectResponse $httpResponse */
        $httpResponse = $redirectBinding->send($messageContext);

        return $httpResponse->getTargetUrl();
    }
}
