<?php

namespace App\Plugins\Authentication;

use App\Plugins\Annotations\Authentication;

/**
 * @Authentication(
 *   id = "cookie",
 *   name = "Cookie",
 * )
 */
class Cookie extends AuthenticationPluginBase
{

    /**
     * Making sure the user is valid.
     */
    public function validateUser()
    {
    }
}
