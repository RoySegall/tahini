<?php

namespace App\Controller\User;

use App\Controller\AbstractTaiazController;
use App\Entity\Main\JobProcess;
use App\Repository\JobProcessRepository;
use App\Services\TaliazAccessToken;
use App\Services\TaliazDoctrine;
use App\Services\TaliazOldProcessor;
use App\Services\TaliazUser;
use App\Services\TaliazValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Get the job processes in the system.
 *
 * @Route("/api/me")
 */
class Me extends AbstractTaiazController {

  /**
   * @Route("", methods={"GET"})
   *
   * @param Request $request
   *  The request service.
   * @param TaliazAccessToken $taliazAccessToken
   *  The taliaz access token service.
   *
   * @return JsonResponse
   */
  public function userDetails(Request $request, TaliazAccessToken $taliazAccessToken) {
    return $this->json('a');
  }

}
