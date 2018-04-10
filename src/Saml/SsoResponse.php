<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Saml;

use DateTime;
use LightSaml\Binding\BindingFactory;
use LightSaml\ClaimTypes;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Helper;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AudienceRestriction;
use LightSaml\Model\Assertion\AuthnContext;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\SamlConstants;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SsoResponse
{
    const SESSION_DURATION_TYPE_ATTRIBUTE_NAME = 'session_duration_type';

    /**
     * @var SamlDataManagerInterface
     */
    private $saml_data_manager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $saml_crt;

    /**
     * @var string
     */
    private $saml_key;

    /**
     * @param SamlDataManagerInterface $saml_data_manager
     * @param string                   $saml_crt
     * @param string                   $saml_key
     * @param LoggerInterface          $logger
     */
    public function __construct(SamlDataManagerInterface $saml_data_manager, $saml_crt, $saml_key, LoggerInterface $logger = null)
    {
        $this->saml_data_manager = $saml_data_manager;
        $this->logger = $logger;
        $this->saml_crt = $saml_crt;
        $this->saml_key = $saml_key;
    }

    /**
     * @param  string $email
     * @param  string $message_id
     * @return string
     */
    public function send($email, $message_id)
    {
        $message = $this->saml_data_manager->get($message_id);

        if (!$message) {
            if ($this->logger) {
                $this->logger->error("Saml message with id $message_id not found or expired");
            }

            throw new RuntimeException('Authentication message does not exist');
        }

        $this->saml_data_manager->delete($message_id);

        $response = new Response();
        $assertion = new Assertion();
        $response
            ->addAssertion($assertion)
            ->setID(Helper::generateID())
            ->setIssueInstant(new DateTime())
            ->setDestination($message->getAssertionConsumerServiceURL())
            ->setIssuer(new Issuer($message->getIssuer()->getValue()));

        $assertion
            ->setId(Helper::generateID())
            ->setIssueInstant(new DateTime())
            ->setIssuer(new Issuer($message->getIssuer()->getValue()))
            ->setSubject(
                (new Subject())
                    ->setNameID(new NameID($email, SamlConstants::NAME_ID_FORMAT_EMAIL))
                    ->addSubjectConfirmation(
                        (new SubjectConfirmation())
                            ->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER)
                            ->setSubjectConfirmationData(
                                (new SubjectConfirmationData())
                                    ->setInResponseTo($message->getID())
                                    ->setNotOnOrAfter(new DateTime('+1 MINUTE'))
                                    ->setRecipient($message->getAssertionConsumerServiceURL())
                            )
                    )
            )
            ->setConditions(
                (new Conditions())
                    ->setNotBefore(new DateTime())
                    ->setNotOnOrAfter(new DateTime('+1 MINUTE'))
                    ->addItem(
                        new AudienceRestriction([$message->getAssertionConsumerServiceURL()])
                    )
            )
            ->addItem(
                (new AttributeStatement())
                    ->addAttribute(new Attribute(ClaimTypes::EMAIL_ADDRESS, $email))
            )
            ->addItem(
                (new AuthnStatement())
                    ->setAuthnInstant(new DateTime('-10 MINUTE'))
                    ->setSessionIndex($message_id)
                    ->setAuthnContext(
                        (new AuthnContext())->setAuthnContextClassRef(SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT)
                    )
            );

        $certificate = X509Certificate::fromFile($this->saml_crt);
        $private_key = KeyHelper::createPrivateKey($this->saml_key, '', true);

        $response->setSignature(new SignatureWriter($certificate, $private_key));

        $binding_factory = new BindingFactory();
        $post_binding = $binding_factory->create(SamlConstants::BINDING_SAML2_HTTP_POST);
        $message_context = new MessageContext();
        $message_context->setMessage($response);
        /** @var SymfonyResponse $http_response */
        $http_response = $post_binding->send($message_context);

        return $http_response->getContent();
    }
}
