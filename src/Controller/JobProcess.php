<?php

namespace App\Controller;

use App\Repository\JobProcessRepository;
use App\Services\TaliazOldProcessor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Get the job processes in the system.
 *
 * @Route("/api/v2/job-processes")
 */
class JobProcess extends AbstractEntityController {

  /**
   * @var array
   */
  protected $mapper = ['userId' => 'user_id', 'labFile' => 'lab_file', 'questionnaireId' => 'questionnaire_id', 'talResults' => 'tal_results', 'modelRuntime' => 'model_runtime', 'errorMessage' => 'error_message', 'modelBeagle' => 'model_beagle',];

  /**
   * @Route("/", methods={"GET"})
   *
   * @param JobProcessRepository $job_process
   *  The job process repository service. The service return a query builder
   *  for the job process entity.
   * @param Request $request
   *  The request object.
   * @param TaliazOldProcessor $processor
   *  The old processor service.
   *
   * @return JsonResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *  Return the list of job process with metadata.
   *
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function getAll(JobProcessRepository $job_process, Request $request, TaliazOldProcessor $processor) {
    $page = (int)$request->get('page', 1);
    $limit = 10;
    $posts = $job_process->getMaxJobProcesses($limit);
    $total = $job_process->getTotalRows();

    $pages = ceil($total / $limit);

    if (1 != $page && $page > $pages) {
      return $this->error('The request page is not in the correct range');
    }

    $processor->setMapper($this->mapper)->processRecords($posts);

    $data = [];
    foreach ($posts as $post) {
      $data[$post->id] = $post;
    }

    return new JsonResponse(['data' => $data, 'total' => $total, 'last' => $page, 'current' => $page, 'limit' => $limit,]);
  }

  /**
   * @Route("/{id}", methods={"GET"})
   *
   * @param int $id
   *  The ID of the job process.
   * @param TaliazOldProcessor $processor
   *  The old processor service.
   *
   * @return JsonResponse
   */
  public function getSingle(int $id, TaliazOldProcessor $processor) {
    $job = $this->getDoctrine()->getRepository(\App\Entity\JobProcess::class)->find($id);

    $processor->setMapper($this->mapper)->processRecord($job);
    return new JsonResponse($job);
  }

  /**
   * @Route("/{id}", methods={"PUT", "PATCH"})
   *
   * @param int $id
   *  The ID of the job process.
   * @param Request $request
   *  The request service.
   * @param ValidatorInterface $validator
   *  The validator service.
   * @param TaliazOldProcessor $processor
   *  The processor service.
   *
   * @return JsonResponse
   */
  public function update(int $id, Request $request, ValidatorInterface $validator, TaliazOldProcessor $processor) {
    /** @var \App\Entity\JobProcess $job */
    $job = $this->getDoctrine()->getRepository(\App\Entity\JobProcess::class)->find($id);

    if (!$job) {
      return $this->error('The is no job process with ' . $id);
    }

    // Change the values.
    if ($error = $this->payloadToEntity($job, $request)) {
      return $error;
    }

    // Checking if there's no errors.
    if ($errors = $this->validate($job, $validator)) {
      return $errors;
    }

    // Updating job.
    $this->updateEntity($job);

    // Convert the new object to the old preview.
    $processor->setMapper($this->mapper)->processRecord($job);

    return new JsonResponse($job);
  }

}
