<?php

namespace App\Plugins\Authentication;

use App\Plugins\Annotations\Authentication;

/**
 * @Authentication(
 *   id = "ip",
 *   name = "IP",
 * )
 */
class IP extends AuthenticationPluginBase
{

  /**
   * Making sure the user is valid.
   */
    public function validateUser()
    {
    }
}
