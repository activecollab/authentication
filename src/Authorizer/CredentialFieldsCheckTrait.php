<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;

trait CredentialFieldsCheckTrait
{
    private function verifyRequiredFields(array $credentials, array $fields): void
    {
        foreach ($fields as $field) {
            if (empty($credentials[$field])) {
                throw new InvalidAuthenticationRequestException();
            }
        }
    }

    private function verifyAlphanumFields(array $credentials, array $fields): void
    {
        foreach ($fields as $field) {
            if (empty($credentials[$field]) || !ctype_alnum($credentials[$field])) {
                throw new InvalidAuthenticationRequestException();
            }
        }
    }

    private function verifyEmailFields(array $credentials, array $fields): void
    {
        foreach ($fields as $field) {
            if (empty($credentials[$field]) || !filter_var($credentials[$field], FILTER_VALIDATE_EMAIL)) {
                throw new InvalidAuthenticationRequestException();
            }
        }
    }
}
