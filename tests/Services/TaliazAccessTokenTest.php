<?php

namespace App\Tests\Controller;

use App\Entity\Personal\User;
use App\Tests\TaliazBaseWebTestCase;

class TaliazAccessTokenTest extends TaliazBaseWebTestCase {

  /**
   * Testing creation of an access token.
   *
   * @throws \Exception
   */
  public function testCreateAccessToken() {
    $user = $this->createUser(false);
    $access_token = $this->getTaliazAccessToken()->createAccessToken($user);

    $this->assertNotEmpty($user->id);
    $this->assertEquals($user->id, $access_token->user->id);
  }

  /**
   * Testing the get access token.
   *
   * @throws \Exception
   */
  public function testGetAccessToken() {
    $user = $this->createUser(false);
    $access_token = $this->getTaliazAccessToken()->createAccessToken($user);

    $this->assertNotEmpty($user->id);
    $this->assertEquals($user->id, $access_token->user->id);
  }

  /**
   * @throws \Exception
   */
  public function testHasAccessToken() {
    $one_user = $this->createUser(false);
    $second_user = $this->createUser(true);

    $access_token = $this->getTaliazAccessToken()->createAccessToken($one_user);

    $this->assertFalse($this->getTaliazAccessToken()->hasAccessToken($second_user));
    $this->assertEquals($access_token, $this->getTaliazAccessToken()->hasAccessToken($one_user));
  }

  /**
   * Testing the refresh token functionality.
   */
  public function testRefreshAccessToken() {
  }

  /**
   * Testing we get the object of the user the string of the access token.
   */
  public function testLoadByAccessToken() {
  }

  /**
   * Testing the that we acquire the access token from the refresh.
   */
  public function testGetAccessTokenFromRequest() {
  }

  /**
   * Testing the access token is revoked from the DB.
   */
  public function testRevokeAccessToken() {
  }

  /**
   * Testing that the string of the access token is removed from the DB.
   */
  public function testClearAccessToken() {
  }

  /**
   * Testing the expires of the access token.
   */
  public function testGetAccessTokenExpires() {
    $this->assertEquals($this->getTaliazAccessToken()->getAccessTokenExpires(), 86400);
  }

}
