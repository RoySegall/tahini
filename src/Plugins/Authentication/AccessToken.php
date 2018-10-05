<?php

namespace App\Plugins\Authentication;

use App\Plugins\Annotations\Authentication;

/**
 * @Authentication(
 *   id = "access_token",
 *   name = "Access Token",
 * )
 */
class AccessToken extends AuthenticationPluginBase
{

  /**
   * Making sure the user is valid.
   */
    public function validateUser()
    {
        return true;
    }
}
