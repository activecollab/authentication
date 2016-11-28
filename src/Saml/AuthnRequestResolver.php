<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Saml;

use Exception;
use LightSaml\Binding\BindingFactory;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\XmlDSig\SignatureStringReader;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthnRequestResolver
{
    /**
     * @var string
     */
    private $saml_crt;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string          $saml_crt
     * @param LoggerInterface $logger
     */
    public function __construct($saml_crt, LoggerInterface $logger = null)
    {
        $this->saml_crt = $saml_crt;
        $this->logger = $logger;
    }

    /**
     * @param  Request|null $request
     * @return AuthnRequest
     */
    public function resolve(Request $request = null)
    {
        if (!$request) {
            $request = Request::createFromGlobals();
        }

        $binding_factory = new BindingFactory();
        $binding = $binding_factory->getBindingByRequest($request);

        $message_context = new MessageContext();
        $binding->receive($request, $message_context);
        $message = $message_context->asAuthnRequest();

        $this->validateSignature($message);

        return $message;
    }

    /**
     * @param  AuthnRequest $message
     * @throws Exception
     */
    private function validateSignature(AuthnRequest $message)
    {
        $key = KeyHelper::createPublicKey(X509Certificate::fromFile($this->saml_crt));

        /** @var SignatureStringReader $signature_reader */
        $signature_reader = $message->getSignature();

        try {
            if ($signature_reader->validate($key)) {
                return;
            }

            throw new Exception('Signature not validated');
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->error("AuthnRequest validation failed with message {$e->getMessage()}.", [
                    'exception' => $e,
                ]);
            }

            throw $e;
        }
    }
}
