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
     * @param  array fields
     */
    private function verifyRequiredFields(array $credentials, array $fields)
    {
        $is_empty = function ($credentials, $field) {
            return isset($credentials[$field]) ? $credentials[$field] === '' : true;
        };

        foreach ($fields as $field) {
            if ($is_empty($credentials, $field)) {
                throw new InvalidAuthenticationRequestException();
            }
        }
    }
}
