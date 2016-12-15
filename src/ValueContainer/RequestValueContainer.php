<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\ValueContainer;

use LogicException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Authentication\ValueContainer
 */
class RequestValueContainer implements RequestValueContainerInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var string
     */
    private $attribute_name;

    /**
     * RequestValueContainer constructor.
     *
     * @param string $attribute_name
     */
    public function __construct($attribute_name)
    {
        $this->attribute_name = $attribute_name;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param  ServerRequestInterface $request
     * @return $this
     */
    public function &setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue()
    {
        if (!$this->getRequest()) {
            throw new LogicException('Request not set.');
        }

        return array_key_exists($this->attribute_name, $this->getRequest()->getAttributes());
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (!$this->getRequest()) {
            throw new LogicException('Request not set.');
        }

        return $this->request->getAttribute($this->attribute_name);
    }
}
