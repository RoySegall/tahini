<?php

namespace App\Controller;

use App\Repository\JobProcessRepository;
use App\Services\TaliazOldProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Get the job processes in the system.
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
    ];

    /**
     * @Route("/api/v2/job-processes", name="job_processes")
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
    public function index(JobProcessRepository $job_process, Request $request, TaliazOldProcessor $processor) {
        $page = (int) $request->get('page', 1);
        $limit = 10;
        $posts = $job_process->getMaxJobProcesses($limit);
        $total = $job_process->getTotalRows();

        $pages = ceil($total / $limit);

        if (1 != $page && $page > $pages) {
            return $this->createNotFoundException();
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
}