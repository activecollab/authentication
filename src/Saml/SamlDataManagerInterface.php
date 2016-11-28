<?php

/*
 * This file is part of the Active Collab Authentication project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Authentication\Saml;

use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\SamlMessage;

interface SamlDataManagerInterface
{
    /**
     * @param SamlMessage $message
     */
    public function set(SamlMessage $message);

    /**
     * @param  string            $message_id
     * @return AuthnRequest|null
     */
    public function get($message_id);

    /**
     * @param string $message_id
     */
    public function delete($message_id);
}
