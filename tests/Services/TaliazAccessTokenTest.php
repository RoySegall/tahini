<?php

namespace App\Tests\Controller;

use App\Entity\Personal\User;
use App\Tests\TaliazBaseWebTestCase;

class TaliazAccessTokenTest extends TaliazBaseWebTestCase {

  /**
   * Testing creation of an access token.
   */
  public function testCreateAccessToken() {
    $user = $this->createUser();
    $access_token = $this->getTaliazAccessToken()->createAccessToken($this->createUser());

    $this->assertEquals(1,1);
  }

  public function testGetAccessToken() {
  }

  public function testHasAccessToken() {
  }

  public function testRefreshAccessToken() {
  }

  public function testLoadByAccessToken() {
  }

  public function testGetAccessTokenFromRequest() {
  }

  public function testRevokeAccessToken() {
  }

  public function testClearAccessToken() {
  }

  /**
   * Testing the expires of the access token.
   */
  public function testGetAccessTokenExpires() {
    $this->assertEquals($this->getTaliazAccessToken()->getAccessTokenExpires(), 86400);
  }

}
