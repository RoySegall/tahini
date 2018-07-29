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
  public function loginController(Request $request, TaliazUser $taliaz_user, TaliazAccessToken $taliazAccessToken) {
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

    $access_token = $taliazAccessToken->getAccessToken($user);

    if ($user != $access_token->id) {
      // It seems that we got an empty access token. This could be due to the
      // face that the access token is no longer valid.
      return $this->error('The access token is no longer valid. Please refresh the token');
    }

    // Yeah, we got an access token. Bring back to the user.
    return $this->json([
      'user_id' => $user->id,
      'expired' => $access_token->expires,
      'refresh_token' => $access_token->access_token,
      'access_token' => $access_token->access_token,
    ]);
  }

}
