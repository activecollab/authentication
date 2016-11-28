<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Test\Saml;

use ActiveCollab\Authentication\Saml\AuthnRequestResolver;
use ActiveCollab\Authentication\Test\TestCase\TestCase;
use LightSaml\Model\Protocol\AuthnRequest;
use Symfony\Component\HttpFoundation\Request;

class AuthnRequestResolverTest extends TestCase
{
    /**
     * @var AuthnRequestResolver
     */
    private $authn_request_resolver;

    /**
     * @var array
     */
    private $get;

    public function setUp()
    {
        parent::setUp();

        $this->authn_request_resolver = new AuthnRequestResolver(__DIR__ . '/../Fixtures/saml.crt');
        $this->get = [
            'SAMLRequest' => 'fZFPb8IwDMXvfIoq95KGltJGpYiNw5CYhqDbYZcpTc3I1CZdnaJ9/IV/EgeEj5af3+/Z2eyvqb0DdKiMnhI2DMgsH2Tz3u71Bn57QOu5CY1T0neaG4EKuRYNILeSb+evKz4aBrztjDXS1MRbLqbkq4Q0TCOIqrQcRZGMRTwOIQ7jSTUpIRHjeJfGQRIGO0a8j6u32+PkiD0sNVqhrWsFLPYZ89m4YAEPE86CT+ItHJTSwp5Ue2tbTmltpKj3Bi1PXFHRKnoYUVW1vnBRQFslhQXirS+gT0pXSn8/TlWeh5C/FMXaX79tC+LNEaE7Wj8bjX0D3Ra6g5Lwvlndh5mcYRjtndBHwGNakmcompqf4nbnC/Nj5zGQuJqT/L6V+8MPSIsZvVmfZ/T2n/ngHw==',
            'SigAlg' => 'http://www.w3.org/2000/09/xmldsig#rsa-sha1',
            'Signature' => 'HD0lpaP4P6zd0oiGaRfKxV0Be65ClLL7BZ1mm0LVZYm4rwg4wZY1aTlf/aq2vPa1zDJ6e5NLbt7HRde4i6PnTVLpKX0ynab2gQQ2aoYDiwBUXw+01pRXYjnCBexfRfOt57pSBqUatuDuXrxKP6bD9nZ4/9pJAtxqva/5IX6ZqiQ3AEuZ9xcZ4cD+AqRFGvUlGu0I4yZzCNeiYpka4Fr340f69Aqr7q/e8ZRyYZJZPACXCK5Iq6nhRE0hBr5ezNPQESrI2te+SRXtnOTiEufPTQi/6roFfWfvwn/DtKGN1JSbt9shzOmQcbbtEq39U1Vr0OB1Ye8Ck6vCV8cRzlZqAQ==',
        ];
    }

    public function testAuthnIsResolved()
    {
        $request = Request::create('http://localhost:8887', 'GET', $this->get);

        $authn_request = $this->authn_request_resolver->resolve($request);

        $this->assertInstanceOf(AuthnRequest::class, $authn_request);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlSecurityException
     * @expectedExceptionMessage Unable to validate signature on query string
     */
    public function testSamlRequestSignatureIsNotValid()
    {
        $this->get['Signature'] = 'invalid';

        $request = Request::create('http://localhost:8887', 'GET', $this->get);

        $this->authn_request_resolver->resolve($request);
    }
}
