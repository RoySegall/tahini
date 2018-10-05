<?php

namespace App\Plugins\Authentication;

use App\Plugins\PluginBase;

/**
 * Class AuthenticationPluginBase
 */
abstract class AuthenticationPluginBase extends PluginBase
{

    /**
     * Making sure the user is valid.
     */
    abstract public function validateUser();
}
