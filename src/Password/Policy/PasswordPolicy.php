<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Password\Policy;

class PasswordPolicy implements PasswordPolicyInterface
{
    public function __construct(
        private int $min_length = 0,
        private bool $require_numbers = false,
        private bool $require_mixed_case = false,
        private bool $require_symbols = false,
    )
    {
    }

    public function getMinLength(): int
    {
        return $this->min_length;
    }

    public function requireNumbers(): bool
    {
        return $this->require_numbers;
    }

    public function requireMixedCase(): bool
    {
        return $this->require_mixed_case;
    }

    public function requireSymbols(): bool
    {
        return $this->require_symbols;
    }

    public function jsonSerialize(): array
    {
        return [
            'min_length' => $this->getMinLength(),
            'require_numbers' => $this->requireNumbers(),
            'require_mixed_case' => $this->requireMixedCase(),
            'require_symbols' => $this->requireSymbols(),
        ];
    }
}
