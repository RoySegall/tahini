<?php

namespace App\Tests\Controller;

use App\Entity\JobProcess;
use App\Tests\TaliazBaseWebTestCase;

/**
 * Testing the home controller.
 *
 * @package App\Tests\Controller
 */
class JobProcessControllerTest extends TaliazBaseWebTestCase {

  /**
   * Testing the job process crud operations.
   */
  public function testHomeController() {
    $job_entry = $this->createJob();

    // Setting up variables section.
    $client = static::createClient();
    $client->request('GET', 'api/v2/job-processes/');

    $response = $client->getResponse();
    $content = json_decode($response->getContent(), true);

    // Checking the status code.
    $this->assertEquals($response->getStatusCode(), 200);

    // Checking the content it self.
    $cloned_entry = clone $job_entry;
    $this->getTaliazOldProcessor()->processRecord($cloned_entry);
    $this->assertNotEmpty($content['data'][$job_entry->id]);

    foreach ($content['data'][$job_entry->id] as $key => $value) {
      $this->assertEquals($value, $cloned_entry->{$key});
    }

    // Check the single entry.
    $client = static::createClient();
    $client->request('GET', 'api/v2/job-processes/' . $job_entry->id);
    $response = $client->getResponse();
    $content = json_decode($response->getContent(), true);
    $this->assertEquals($response->getStatusCode(), 200);

    // Testing the single entry.
    foreach ($content as $key => $value) {
      $this->assertEquals($value, $cloned_entry->{$key});
    }

    // Testing the update of the entry.
    $client = static::createClient();
    $client->request(
      'PATCH',
      'api/v2/job-processes/' . $job_entry->id,
      array(),
      array(),
      array('CONTENT_TYPE' => 'application/json'));

    $results = json_decode($client->getResponse()->getContent(), true);
    $this->assertEquals($results, ['error' => 'The post is empty']);

    $client = static::createClient();
    $client->request(
      'PATCH',
      'api/v2/job-processes/' . $job_entry->id,
      array(),
      array(),
      array('CONTENT_TYPE' => 'application/json'),
      '{"model_beagle": 52}');

    $results = json_decode($client->getResponse()->getContent(), true);

    $this->assertEquals($results, [
      'error' => [
        'message' => 'There are some errors in your request',
        'errors' => [
          'model_beagle' =>  [
            0 => "The allowed values are '0' or '1'"
          ],
        ],
      ],
    ]);

    $client = static::createClient();
    $client->request(
      'PATCH',
      'api/v2/job-processes/' . $job_entry->id,
      array(),
      array(),
      array('CONTENT_TYPE' => 'application/json'),
      '{"model_beagle": "0"}');

    /** @var JobProcess $new_job_entry */
    $new_job_entry = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository(JobProcess::class)
      ->find($job_entry->id);

    $this->assertEquals($new_job_entry->model_beagle, 0);
    $this->assertEquals($job_entry->modelBeagle, 1);
  }

  /**
   * Create a job and test the process along the way.
   *
   * @return JobProcess
   */
  protected function createJob() : JobProcess {
    // Get the manager.
    $entity_manager = $this->getDoctrine()->getManager();

    // Create a job entry.
    $job_entry = new JobProcess();
    $job_entry->status = 1;
    $job_entry->userId = 1;
    $job_entry->questionnaireId = 2;

    $validations = $this->getTaliazValidator()->validate($job_entry);
    $this->assertEquals($validations, ['model_beagle' => [0 => 'This value should not be null.']]);

    $job_entry->modelBeagle = 23;
    $validations = $this->getTaliazValidator()->validate($job_entry);
    $this->assertEquals($validations, ['model_beagle' => [0 => "The allowed values are '0' or '1'"]]);

    $job_entry->modelBeagle = "1";
    $validations = $this->getTaliazValidator()->validate($job_entry);
    $this->assertEquals([], $validations);

    $entity_manager->persist($job_entry);
    $entity_manager->flush();

    return $job_entry;
  }

}
