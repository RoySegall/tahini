<?php

namespace App\Tests\Controller;

use App\Entity\Personal\AccessToken;
use App\Services\TaliazAccessToken;
use App\Tests\TaliazBaseWebTestCase;

class TaliazAccessTokenTest extends TaliazBaseWebTestCase {

  /**
   * Testing creation of an access token.
   *
   * @throws \Exception
   */
  public function testCreateAccessToken() {
    // Making sure the createAccessToken creates an access token.
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
    // Making sure the getAccessToken returns a token.
    $user = $this->createUser(false);
    $access_token = $this->getTaliazAccessToken()->getAccessToken($user);

    $this->assertNotEmpty($user->id);
    $this->assertEquals($user->id, $access_token->user->id);
  }

  /**
   * @throws \Exception
   */
  public function testHasAccessToken() {
    // Creating two users and making sure one has a token and one don't.
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
    // Get some basic elements.
    $user = $this->createUser(false);
    $access_token = $this->getTaliazAccessToken()->createAccessToken($user);

    // Save the ID for later.
    $id = $access_token->id;

    // Making sure the old token still exists and create a new one.
    $this->assertNotEmpty($this->getTaliazDoctrine()->getAccessTokenRepository()->find($id));
    $new_access_token = $this->getTaliazAccessToken()->refreshAccessToken($access_token->refresh_token);

    // Checking there is a new token in town.
    $this->assertNotEquals($new_access_token->id, $id);

    // Making sure we can load the new token and not the old one.
    $this->assertEmpty($this->getTaliazDoctrine()->getAccessTokenRepository()->find($id));
    $this->assertNotEmpty($this->getTaliazDoctrine()->getAccessTokenRepository()->find($new_access_token->id));
  }

  /**
   * Testing we get the object of the user the string of the access token.
   */
  public function testLoadByAccessToken() {
    $user = $this->createUser(false);
    $access_token = $this->getTaliazAccessToken()->createAccessToken($user);
    $this->assertEquals($access_token, $this->getTaliazAccessToken()->loadByAccessToken($access_token->access_token));
  }

  /**
   * Testing the that we acquire the access token from the refresh.
   */
  public function testGetAccessTokenFromRequest() {
    $request = new \Symfony\Component\HttpFoundation\Request();

    // Making an request with a bad access token.
    $request->headers->set(TaliazAccessToken::ACCESS_TOKEN_HEADER_KEY, time());
    $bad_access_token = $this->getTaliazAccessToken()->getAccessTokenFromRequest($request);

    $this->assertNull($bad_access_token->id);

    // Create a new access token and make sure we get the access token via the
    // request.
    $access_token = $this->getTaliazAccessToken()->createAccessToken($this->createUser(false));
    $request->headers->set(TaliazAccessToken::ACCESS_TOKEN_HEADER_KEY, $access_token->access_token);
    $this->assertEquals($access_token, $this->getTaliazAccessToken()->getAccessTokenFromRequest($request));
  }

  /**
   * Testing the access token is revoked from the DB.
   */
  public function testRevokeAccessToken() {
    $access_token = $this->getTaliazAccessToken()->createAccessToken($this->createUser(false));

    $id = $access_token->id;

    // Making sure the old token still exists.
    $this->assertNotEmpty($this->getTaliazDoctrine()->getAccessTokenRepository()->find($id));
    $this->getTaliazAccessToken()->revokeAccessToken($access_token);
    $this->assertEmpty($this->getTaliazDoctrine()->getAccessTokenRepository()->find($id));
  }

  /**
   * Testing that the string of the access token is removed from the DB.
   */
  public function testClearAccessToken() {
    $access_token = $this->getTaliazAccessToken()->createAccessToken($this->createUser(false));

    $id = $access_token->id;

    // Get the access token.
    /** @var AccessToken $tokens */
    $db_token = $this->getTaliazDoctrine()->getAccessTokenRepository()->find($id);
    $this->assertEquals($db_token->access_token, $access_token->access_token);

    // Clear the access token for the DB.
    $this->getTaliazAccessToken()->clearAccessToken($access_token);

    $db_token = $this->getTaliazDoctrine()->getAccessTokenRepository()->find($id);
    $this->assertNull($db_token->access_token);
  }

  /**
   * Testing the expires of the access token.
   */
  public function testGetAccessTokenExpires() {
    $this->assertEquals($this->getTaliazAccessToken()->getAccessTokenExpires(), 86400);
  }

}
