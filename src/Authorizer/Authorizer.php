<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessorInterface;

/**
 * @package ActiveCollab\Authentication\Authorizer
 */
abstract class Authorizer implements AuthorizerInterface
{
    /**
     * @var RequestProcessorInterface
     */
    private $request_processor;

    /**
     * {@inheritdoc}
     */
    public function getRequestProcessor()
    {
        return $this->request_processor;
    }

    /**
     * @param  RequestProcessorInterface|null $request_processor
     * @return $this
     */
    protected function &setRequestProcessor(RequestProcessorInterface $request_processor = null)
    {
        $this->request_processor = $request_processor;

        return $this;
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
