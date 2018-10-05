<?php

namespace App\Tests\Controller;

use App\Entity\AccessToken;
use App\Tests\TahiniBaseWebTestCase;

/**
 * Testing login controller.
 *
 * @package App\Tests\Controller
 */
class MeTest extends TahiniBaseWebTestCase {

  /**
   * Testing that the endpoint returns the user object.
   */
  public function testMe() {
    // Create a user and an access token.
    $access_token = $this->getTahiniAccessToken()->createAccessToken($this->createUser(false));

    // Checking the me endpoint.
    $client = static::createClient();
    $client->request('GET', '/api/me', array(), array(), array('HTTP_' . \App\Services\TahiniAccessToken::ACCESS_TOKEN_HEADER_KEY => 'a'));

    $this->assertEquals($client->getResponse()->getContent(), '{"message":"You are not valid. Try again later."}');

    $client = static::createClient();
    $client->request('GET', '/api/me', array(), array(), array('HTTP_' . \App\Services\TahiniAccessToken::ACCESS_TOKEN_HEADER_KEY => $access_token->access_token));

    $decoded_request = json_decode($client->getResponse()->getContent());

    $this->assertEquals($decoded_request->id, $access_token->user->id);

    $access_token = $this->getTahiniAccessToken()->createAccessToken($this->createUser(false));

    // Set the token as un-valid.
    $entity_manager = $this->getDoctrine()->getManager();

    $access_token = $entity_manager->find(AccessToken::class, $access_token->id);
    $access_token->expires = time() - 10;

    $entity_manager->persist($access_token);
    $entity_manager->flush();

    // Check again.
    $client = static::createClient();
    $client->request('GET', '/api/me', array(), array(), array('HTTP_' . \App\Services\TahiniAccessToken::ACCESS_TOKEN_HEADER_KEY => $access_token->access_token));
    $this->assertEquals($client->getResponse()->getContent(), '{"message":"You are not valid. Try again later."}');
  }
}
