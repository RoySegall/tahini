<?php

namespace App\Controller;

use App\Entity\JobProcess;
use App\Repository\JobProcessRepository;
use App\Services\TaliazOldProcessor;
use App\Services\TaliazValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Get the job processes in the system.
 *
 * @Route("/api/v2/job-processes")
 */
class JobProcessController extends AbstractEntityController {

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

    $job_entity = new JobProcess();
    $processor->setMapper($job_entity->getMapper())->processRecords($posts);

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
   * @return JsonResponse
   */
  public function getSingle(int $id, TaliazOldProcessor $processor) {
    $job_entity = new \App\Entity\JobProcess();

    $job = $this->getDoctrine()->getRepository($job_entity)->find($id);

    $processor->setMapper($job_entity->getMapper())->processRecord($job);
    return new JsonResponse($job);
  }

  /**
   * @Route("/{id}", methods={"PUT", "PATCH"})
   *
   * @param int $id
   *  The ID of the job process.
   * @param Request $request
   *  The request service.
   * @param \App\Services\TaliazValidator $taliaz_validator
   *  The validator service.
   * @param TaliazOldProcessor $processor
   *  The processor service.
   *
   * @return JsonResponse
   */
  public function update(int $id, Request $request, TaliazValidator $taliaz_validator, TaliazOldProcessor $processor) {
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
    if ($errors = $taliaz_validator->validate($job)) {
      return $this->error(['message' => 'There are some errors in your request', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    // Updating job.
    $this->updateEntity($job);

    // Convert the new object to the old preview.
    $processor->setMapper($job->getMapper())->processRecord($job);

    return new JsonResponse($job);
  }

}
