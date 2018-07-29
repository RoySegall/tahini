<?php

namespace App\Controller\User;

use App\Controller\AbstractTaiazController;
use App\Entity\Main\JobProcess;
use App\Repository\JobProcessRepository;
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
 * @Route("/api/user/login")
 */
class Login extends AbstractTaiazController {

  /**
   * @Route("", methods={"POST"})
   *
   * @param Request $request
   *  The request service.
   *
   * @return
   */
  public function loginController(Request $request, TaliazUser $taliaz_user) {
    $payload = $this->processPayload($request);

    if (!$auth = $payload->get('auth')) {
      return $this->error("The payload is not correct.", Response::HTTP_BAD_REQUEST);
    }

    list($date, $username, $password) = explode("_", base64_decode($auth));

    if ($date != date("d/m/Y")) {
      return $this->error("The payload is not correct", Response::HTTP_BAD_REQUEST);
    }

    if (!$user = $taliaz_user->findUserByUsername($username, $password)) {
      return $this->error("Username and password are in correct.");
    }

    return $this->json($user);
  }

}
