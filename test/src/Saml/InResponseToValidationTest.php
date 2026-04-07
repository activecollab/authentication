<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Test\Saml;

use ActiveCollab\Authentication\Saml\Exception\InvalidSamlResponseException;
use ActiveCollab\Authentication\Saml\SamlDataManagerInterface;
use ActiveCollab\Authentication\Saml\SamlUtils;
use ActiveCollab\Authentication\Saml\SsoResponse;
use ActiveCollab\Authentication\Test\Saml\Fixtures\InMemorySamlRequestStateStore;
use ActiveCollab\Authentication\Test\TestCase\TestCase;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\Response;

class InResponseToValidationTest extends TestCase
{
    private SamlUtils $saml_utils;
    private SsoResponse $sso_response;
    private SamlDataManagerInterface $saml_data_manager;
    private InMemorySamlRequestStateStore $store;
    private string $idp_certificate;
    private string $expected_destination = 'http://localhost:8887/projects';
    private string $expected_audience = 'http://localhost:8887/projects';

    public function setUp(): void
    {
        parent::setUp();

        $this->saml_utils = new SamlUtils();
        $this->saml_data_manager = $this->createMock(SamlDataManagerInterface::class);
        $crt_path = __DIR__ . '/../Fixtures/saml.crt';
        $key_path = __DIR__ . '/../Fixtures/saml.key';
        $this->idp_certificate = file_get_contents($crt_path);
        $this->sso_response = new SsoResponse($this->saml_data_manager, $crt_path, $key_path);
        $this->store = new InMemorySamlRequestStateStore();
    }

    private function generateResponse(string $email, string $message_id, string $authn_request_id): string
    {
        $authn_request = new AuthnRequest();
        $authn_request->setID($authn_request_id);
        $authn_request->setAssertionConsumerServiceURL($this->expected_destination);
        $authn_request->setIssuer(new Issuer('http://localhost/idp'));

        $this->saml_data_manager->expects($this->any())->method('get')->with($message_id)->willReturn($authn_request);

        return $this->sso_response->send($email, $message_id);
    }

    public function testValidInResponseToPassesValidation(): void
    {
        $message_id = '123abc';
        $authn_request_id = '_f6d3b434-629a-4c91-998a-7889e496359b';
        
        $response_html = $this->generateResponse('owner@company.com', $message_id, $authn_request_id);
        
        if (preg_match('/name="SAMLResponse" value="([^"]+)"/', $response_html, $matches)) {
            $saml_response_b64 = $matches[1];
        } else {
            $this->fail('Could not extract SAMLResponse from HTML');
        }

        $this->store->save($authn_request_id, 3600);

        $payload = ['SAMLResponse' => $saml_response_b64];
        $parsed_response = $this->saml_utils->parseSamlResponse(
            $payload,
            $this->idp_certificate,
            $this->expected_destination,
            $this->expected_audience,
            $this->store
        );

        $this->assertSame($authn_request_id, $parsed_response->getInResponseTo());
    }

    public function testInResponseToNotInStoreIsRejected(): void
    {
        $this->expectException(InvalidSamlResponseException::class);
        $this->expectExceptionMessage('SAML response InResponseTo does not match any pending authentication request.');

        $message_id = '123abc';
        $authn_request_id = '_f6d3b434-629a-4c91-998a-7889e496359b';
        
        $response_html = $this->generateResponse('owner@company.com', $message_id, $authn_request_id);
        
        if (preg_match('/name="SAMLResponse" value="([^"]+)"/', $response_html, $matches)) {
            $saml_response_b64 = $matches[1];
        } else {
            $this->fail('Could not extract SAMLResponse from HTML');
        }

        $payload = ['SAMLResponse' => $saml_response_b64];
        $this->saml_utils->parseSamlResponse(
            $payload,
            $this->idp_certificate,
            $this->expected_destination,
            $this->expected_audience,
            $this->store
        );
    }

    public function testReplayIsRejected(): void
    {
        $message_id = '123abc';
        $authn_request_id = '_f6d3b434-629a-4c91-998a-7889e496359b';
        
        $response_html = $this->generateResponse('owner@company.com', $message_id, $authn_request_id);
        
        if (preg_match('/name="SAMLResponse" value="([^"]+)"/', $response_html, $matches)) {
            $saml_response_b64 = $matches[1];
        } else {
            $this->fail('Could not extract SAMLResponse from HTML');
        }

        $this->store->save($authn_request_id, 3600);

        $payload = ['SAMLResponse' => $saml_response_b64];

        $this->saml_utils->parseSamlResponse(
            $payload,
            $this->idp_certificate,
            $this->expected_destination,
            $this->expected_audience,
            $this->store
        );

        $this->expectException(InvalidSamlResponseException::class);
        $this->expectExceptionMessage('SAML response InResponseTo does not match any pending authentication request.');
        
        $this->saml_utils->parseSamlResponse(
            $payload,
            $this->idp_certificate,
            $this->expected_destination,
            $this->expected_audience,
            $this->store
        );
    }

    public function testMissingInResponseToIsRejectedWhenStoreIsProvided(): void
    {
        $message_id = '123abc';
        $authn_request_id = '_f6d3b434-629a-4c91-998a-7889e496359b';
        
        $response_html = $this->generateResponse('owner@company.com', $message_id, $authn_request_id);
        
        if (preg_match('/name="SAMLResponse" value="([^"]+)"/', $response_html, $matches)) {
            $saml_response_b64 = $matches[1];
        } else {
            $this->fail('Could not extract SAMLResponse from HTML');
        }

        $xml = base64_decode($saml_response_b64);
        $xml = preg_replace('/InResponseTo="[^"]+"/', '', $xml);

        $deserialization_context = new DeserializationContext();
        $deserialization_context->getDocument()->loadXML($xml);

        $saml_response = new Response();
        $saml_response->deserialize($deserialization_context->getDocument()->firstChild, $deserialization_context);

        $this->expectException(InvalidSamlResponseException::class);
        $this->expectExceptionMessage('SAML response is missing InResponseTo attribute.');

        $this->saml_utils->validateAssertionConditions(
            $saml_response,
            $this->expected_destination,
            $this->expected_audience,
            $this->store,
        );
    }
}
