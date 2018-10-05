<?php

namespace App\Controller\User;

use App\Controller\AbstractTaiazController;
use App\Services\TahiniAccessToken;
use App\Services\TahiniUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Get the job processes in the system.
 *
 * @Route("/api/user/")
 */
class Login extends AbstractTaiazController
{

    const DATE_FORMAT = 'd/m/Y';

  /**
   * @Route("login", methods={"POST"})
   *
   * @param Request $request
   *  The request service.
   * @param TahiniUser $tahini_user
   *  The tahini user service.
   * @param TahiniAccessToken $tahiniAccessToken
   *  The tahini access token service.
   *
   * @return JsonResponse
   */
    public function loginController(Request $request, TahiniUser $tahini_user, TahiniAccessToken $tahiniAccessToken)
    {
        if (!$payload = $this->processPayload($request)) {
            return $this->error("The payload is not correct.", Response::HTTP_BAD_REQUEST);
        }

        if (!$auth = $payload->get('auth')) {
            return $this->error("The payload is not correct.", Response::HTTP_BAD_REQUEST);
        }

        $exploded_auth = explode("_", base64_decode($auth));

        if (count($exploded_auth) != 3) {
            return $this->error("The payload is not correct.", Response::HTTP_BAD_REQUEST);
        }

        list($date, $username, $password) = $exploded_auth;

        if ($date != date(self::DATE_FORMAT)) {
            return $this->error("The payload is not correct", Response::HTTP_BAD_REQUEST);
        }

        if (!$user = $tahini_user->findUserByUsername($username, $password)) {
            return $this->error("Username and password are in correct.");
        }

        $access_token = $tahiniAccessToken->getAccessToken($user);

        if (empty($access_token->access_token)) {
          // It seems that we got an empty access token. This could be due to the
          // face that the access token is no longer valid.
            return $this->error('The access token is no longer valid. Please refresh the token');
        }

      // Yeah, we got an access token. Bring back to the user.
        return $this->json([
        'user_id' => $user->id,
        'expires' => $access_token->expires,
        'access_token' => $access_token->access_token,
        'refresh_token' => $access_token->refresh_token,
        ]);
    }

  /**
   * @Route("refresh", methods={"POST"})
   *
   * @param Request $request
   *  The request service.
   * @param TahiniAccessToken $tahiniAccessToken
   *  The tahini access token service.
   *
   * @return JsonResponse
   */
    public function refreshToken(Request $request, TahiniAccessToken $tahiniAccessToken)
    {

        if (!$payload = $this->processPayload($request)) {
            return $this->error("The payload is not correct.", Response::HTTP_BAD_REQUEST);
        }

        if (!$refresh_token = $payload->get('refresh_token')) {
            return $this->error('The refresh token is missing', Response::HTTP_BAD_REQUEST);
        }

        $access_token = $tahiniAccessToken->refreshAccessToken($refresh_token);

        if (empty($access_token->id)) {
            return $this->error('It seems that the given refresh token does not exists.');
        }

        return $this->json([
        'user_id' => $access_token->user->id,
        'expires' => $access_token->expires,
        'access_token' => $access_token->access_token,
        'refresh_token' => $access_token->refresh_token,
        ]);
    }
}
