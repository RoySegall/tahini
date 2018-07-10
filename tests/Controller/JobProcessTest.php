<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Testing the job process end point.
 *
 * @package App\Tests\Controller
 */
class JobProcessTest extends WebTestCase {

  /**
   * Testing the job process crud operations.
   */
  public function testJobProcessCrud() {
    $client = static::createClient();

    $client->request('GET', '/api/v2/job-processes');

    $this->assertEquals(200, $client->getResponse()->getStatusCode());
  }

}