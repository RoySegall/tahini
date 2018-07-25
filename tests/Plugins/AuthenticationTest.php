<?php

namespace App\Tests\Controller;

use App\Plugins\Authentication;
use App\Tests\TaliazBaseWebTestCase;

class AuthenticationTest extends TaliazBaseWebTestCase {

  /**
   * Testing the plugin discovery.
   */
  public function testAuthenticationDiscovery() {
    $plugins = $this->getAuthenticationService()->getPlugins();

    $this->assertTrue(in_array('cookie', array_keys($plugins)));
    $this->assertTrue(in_array('access_token', array_keys($plugins)));
    $this->assertTrue(in_array('ip', array_keys($plugins)));
  }

  /**
   * Testing the negotiation of the authenticator.
   */
  public function testAuthenticationNegotiate() {
    // todo: test other cases - when we don't have access token key etc. etc.
    $this->assertEquals('App\Plugins\Authentication\AccessToken', get_class($this->getAuthenticationService()->negotiate()));
  }

  /**
   * Get the authentication service.
   *
   * @return Authentication
   *  The authentication service.
   */
  public function getAuthenticationService() : Authentication {
    return $this->getContainer()->get('App\Plugins\Authentication');
  }

}

