<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Password\Policy;

/**
 * @package ActiveCollab\Authentication\Test\Fixtures
 */
class PasswordPolicy implements PasswordPolicyInterface
{
    /**
     * @var int
     */
    private $min_length;

    /**
     * @var bool
     */
    private $require_numbers;

    /**
     * @var bool
     */
    private $require_mixed_case;

    /**
     * @var bool
     */
    private $require_symbols;

    /**
     * @param int  $min_length
     * @param bool $require_numbers
     * @param bool $require_mixed_case
     * @param bool $require_symbols
     */
    public function __construct($min_length = 0, $require_numbers = false, $require_mixed_case = false, $require_symbols = false)
    {
        $this->min_length = $min_length;
        $this->require_numbers = $require_numbers;
        $this->require_mixed_case = $require_mixed_case;
        $this->require_symbols = $require_symbols;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinLength()
    {
        return $this->min_length;
    }

    /**
     * {@inheritdoc}
     */
    public function requireNumbers()
    {
        return $this->require_numbers;
    }

    /**
     * {@inheritdoc}
     */
    public function requireMixedCase()
    {
        return $this->require_mixed_case;
    }

    /**
     * {@inheritdoc}
     */
    public function requireSymbols()
    {
        return $this->require_symbols;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'min_length' => $this->getMinLength(),
            'require_numbers' => $this->requireNumbers(),
            'require_mixed_case' => $this->requireMixedCase(),
            'require_symbols' => $this->requireSymbols(),
        ];
    }
}
