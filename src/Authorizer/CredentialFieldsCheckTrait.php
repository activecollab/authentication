<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Authorizer;

use ActiveCollab\Authentication\Exception\InvalidAuthenticationRequestException;

/**
 * @package ActiveCollab\Authentication\Authorizer
 */
trait CredentialFieldsCheckTrait
{
    /**
     * @param array $credentials
     * @param array $fields
     */
    private function verifyRequiredFields(array $credentials, array $fields)
    {
        foreach ($fields as $field) {
            if (empty($credentials[$field])) {
                throw new InvalidAuthenticationRequestException();
            }
        }
    }

    /**
     * @param array $credentials
     * @param array $fields
     */
    private function verifyAlphanumFields(array $credentials, array $fields)
    {
        foreach ($fields as $field) {
            if (empty($credentials[$field]) || !ctype_alnum($credentials[$field])) {
                throw new InvalidAuthenticationRequestException();
            }
        }
    }

    /**
     * @param array $credentials
     * @param array $fields
     */
    private function verifyEmailFields(array $credentials, array $fields)
    {
        foreach ($fields as $field) {
            if (empty($credentials[$field]) || !filter_var($credentials[$field], FILTER_VALIDATE_EMAIL)) {
                throw new InvalidAuthenticationRequestException();
            }
        }
    }
}
