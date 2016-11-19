<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer\RequestAware;

use ActiveCollab\Authentication\Authorizer\RequestProcessor\RequestProcessorInterface;

/**
 * @package ActiveCollab\Authentication\Authorizer\RequestAware
 */
trait RequestAware
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
}
