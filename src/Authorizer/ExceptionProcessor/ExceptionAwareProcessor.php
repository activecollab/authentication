<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer\ExceptionProcessor;

use ActiveCollab\Authentication\Authorizer\ExceptionAware\ExceptionAwareInterface;

/**
 * @package ActiveCollab\Authentication\Authorizer\ExceptionProcessor
 */
trait ExceptionAwareProcessor
{
    /**
     * @var ExceptionAwareInterface
     */
    private $exception_processor;

    /**
     * {@inheritdoc}
     */
    public function getExceptionProcessor()
    {
        return $this->exception_processor;
    }

    /**
     * @param  ExceptionAwareInterface|null $exception_processor
     * @return $this
     */
    protected function &setExceptionProcessor(ExceptionAwareInterface $exception_processor = null)
    {
        $this->exception_processor = $exception_processor;

        return $this;
    }
}
