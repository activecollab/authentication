<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessingResult;

/**
 * @package ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessingResult
 */
class RequestProcessingResult implements RequestProcessingResultInterface
{
    /**
     * @var array
     */
    private $credentials;

    /**
     * @var mixed|null
     */
    private $default_payload;

    /**
     * RequestProcessingResult constructor.
     *
     * @param array $credentials
     * @param mixed $default_payload
     */
    public function __construct(array $credentials, $default_payload = null)
    {
        $this->credentials = $credentials;
        $this->default_payload = $default_payload;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultPayload()
    {
        return $this->default_payload;
    }
}
