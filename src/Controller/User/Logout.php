<?php

namespace App\Controller\User;

use App\Controller\AbstractTaiazController;
use App\Entity\Main\JobProcess;
use App\Repository\JobProcessRepository;
use App\Services\TahiniAccessToken;
use App\Services\TahiniDoctrine;
use App\Services\TahiniUser;
use App\Services\TahiniValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Get the job processes in the system.
 *
 * @Route("/api/user/")
 */
class Logout extends AbstractTaiazController
{

  /**
   * @Route("logout", methods={"GET"})
   *
   * @param Request $request
   *  The request service.
   * @param TahiniAccessToken $tahiniAccessToken
   *  The tahini access token service.
   *
   * @return JsonResponse
   */
    public function revoking(Request $request, TahiniAccessToken $tahiniAccessToken)
    {
        $tahiniAccessToken->revokeAccessToken($tahiniAccessToken->getAccessTokenFromRequest($request));
        return $this->json('The access token has been removed.');
    }
}
