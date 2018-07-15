<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Testing the home controller.
 *
 * @package App\Tests\Controller
 */
class HomeControllerTest extends WebTestCase {

  /**
   * Testing the job process crud operations.
   */
  public function testHomeController() {
    $client = static::createClient();

    $client->request('GET', '/');

    $response = $client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals(json_decode($response->getContent(), true), [
      'title' => 'Taliaz Health',
      'Sys Admin' => [
        'name' => 'Sagee Lupin',
        'email' => 'tech.team@taliazhealth.com',
      ],
      'version' => '2.1',
      'env' => 'test',
      'ip_address' => '127.0.0.1',
    ]);
  }

}