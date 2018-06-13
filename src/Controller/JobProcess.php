<?php

namespace App\Controller;

use App\Repository\JobProcessRepository;
use App\Services\TaliazOldProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Get the job processes in the system.
 *
 * @Route("/api/v2/job-processes")
 */
class JobProcess extends AbstractController {

    /**
     * @var array
     */
    protected $mapper = [
        'userId' => 'user_id',
        'labFile' => 'lab_file',
        'questionnaireId' => 'questionnaire_id',
        'talResults' => 'tal_results',
        'modelRuntime' => 'model_runtime',
        'errorMessage' => 'error_message',
        'modelBeagle' => 'model_beagle',
    ];

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
        $page = (int) $request->get('page', 1);
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

        return new JsonResponse([
            'data' => $data,
            'total' => $total,
            'last' => $page,
            'current' => $page,
            'limit' => $limit,
        ]);
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
        $job = $this
            ->getDoctrine()
            ->getRepository(\App\Entity\JobProcess::class)
            ->find($id);

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
     *
     * @return JsonResponse
     */
    public function update(int $id, Request $request, ValidatorInterface $validator, TaliazOldProcessor $processor) {
        $job = $this
            ->getDoctrine()
            ->getRepository(\App\Entity\JobProcess::class)
            ->find($id);

        if (!$job) {
            return $this->error('The is no job process with ' . $id);
        }

        if (!$new_data = $this->processPayload($request)) {
            return $this->error('The post is empty', Response::HTTP_BAD_REQUEST);
        }

        // Change the values.
        $flipped = array_flip($this->mapper);

        foreach ($new_data as $key => $value) {
            $job->{$flipped[$key]} = $value;
        }

        if ($errors = $this->validate($job, $validator)) {
            return $errors;
        }

        $processor->setMapper($this->mapper)->processRecord($job);

        return new JsonResponse($job);
    }

    /**
     * Validating the entity.
     *
     * todo: move to service.
     *
     * @param $entity
     * @param ValidatorInterface $validator
     *
     * @return JsonResponse
     */
    protected function validate($entity, ValidatorInterface $validator) {
        $errors = $validator->validate($entity);

        $error_list = [];
        foreach ($errors as $property => $error) {
            $human_property = $this->mapper[$error->getPropertyPath()];
            $error_list[$human_property][] = $error->getMessage();
        }

        if ($error_list) {
            return $this->error(['message' => 'There are some errors in your request', 'errors' => $error_list], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Processing the payload.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     *   Return the payload as an object.
     */
    protected function processPayload(Request $request) {
        $content = $request->getContent();

        if (!$decoded = json_decode($content, true)) {
            return;
        }

        return new ArrayCollection($decoded);
    }

    /**
     * Return a JSON error response.
     *
     * @param $error
     *  The error.
     * @param int $code
     *  The response code. Default to 404.
     *
     * @return JsonResponse
     */
    protected function error($error, $code = Response::HTTP_NOT_FOUND) {
        return $this->json(['error' => $error], $code);
    }
}