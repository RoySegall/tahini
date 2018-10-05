<?php

namespace App\Tests\Controller;

use App\Tests\TahiniBaseWebTestCase;

/**
 * Testing login controller.
 *
 * @package App\Tests\Controller
 */
class LoginTest extends TahiniBaseWebTestCase {

  /**
   * Testing the login controller.
   *
   * @throws \Exception
   */
  public function testLoginController() {
    $user = $this->createUser(true);

    // Try to fail the login controller.
    $client = static::createClient();
    $client->request('POST', '/api/user/login');

    $this->assertEquals($client->getResponse()->getContent(), '{"error":"The payload is not correct."}');

    $client = static::createClient();
    $client->request('POST', '/api/user/login', array(), array(), array('CONTENT_TYPE' => 'application/json'), '{"auth":"pizza"}');

    $this->assertEquals($client->getResponse()->getContent(), '{"error":"The payload is not correct."}');

    // Create access token and start to check it's attached to the correct user.
    $auth = $this->getAuthString($user->username);

    $client = static::createClient();
    $client->request('POST', '/api/user/login', array(), array(), array('CONTENT_TYPE' => 'application/json'), '{"auth":"' . $auth . '"}');

    $response = $client->getResponse()->getContent();
    $decoded_response = json_decode($response);

    // Checking the access token belong to the user.
    $this->assertEquals($user->id, $decoded_response->user_id);

    // Checking the expires is valid for 1 day.
    $this->assertTrue($decoded_response->expires - time() > 86000);

    // Making sure we got the access token belongs to the user we created.
    $this->assertEquals($user->id, $this->getTahiniAccessToken()->loadByAccessToken($decoded_response->access_token)->user->id);
  }

  /**
   * Testing the refresh token works.
   */
  public function testRefreshToken() {
    $client = static::createClient();

    $client->request('POST', '/api/user/refresh');
    $this->assertEquals($client->getResponse()->getContent(), '{"error":"The payload is not correct."}');

    $client = static::createClient();
    $client->request('POST', '/api/user/refresh', array(), array(), array('CONTENT_TYPE' => 'application/json'), '{"auth":"pizza"}');

    $this->assertEquals($client->getResponse()->getContent(), '{"error":"The refresh token is missing"}');

    // Get the access token.
    $access_token = $this->getTahiniAccessToken()->getAccessToken($this->createUser(false));

    // Refreshing the access token.
    $client = static::createClient();
    $client->request('POST', '/api/user/refresh', array(), array(), array('CONTENT_TYPE' => 'application/json'), '{"refresh_token":"' . $access_token->refresh_token . '"}');

    $decoded_response = json_decode($client->getResponse()->getContent());

    $new_access_token = $this->getTahiniAccessToken()->loadByAccessToken($decoded_response->access_token);
    $this->assertNotEquals($new_access_token->id, $access_token->id);
  }

  /**
   * Get the auth string for login.
   *
   * @param string $username
   *  The name of the user.
   *
   * @return string
   */
  protected function getAuthString(string $username) : string {
    return base64_encode(date(\App\Controller\User\Login::DATE_FORMAT) . '_' . $username . '_text');
  }

}
