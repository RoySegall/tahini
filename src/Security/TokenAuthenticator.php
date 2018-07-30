<?php

namespace App\Security;

use PHPUnit\Runner\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{

  /**
   * @var array
   *
   * List of routes which anonymous user are allowed to access.
   *
   * Can be a regex pattern or a simple text.
   */
  protected $allowed_anonymous_paths = [
    '/',
    '/api/user/login',
    '/api/user/refresh',
    '(api\/v2\/job-processes\/)[0-9]'
  ];

  /**
   * Called on every request to decide if this authenticator should be
   * used for the request. Returning false will cause this authenticator
   * to be skipped.
   */
  public function supports(Request $request) {
    $path = $request->getRequestUri();

    // Check first if we need to skip the access token auth for paths which
    // anonymous users have access.
    if (!in_array($request->getRequestUri(), $this->allowed_anonymous_paths)) {
      // The path does not exists in a simple format. Check the regex format.
      foreach ($this->allowed_anonymous_paths as $allowed_anonymous_path) {
        if (@preg_match($allowed_anonymous_path . '/m', $path)) {
          return false;
        }
      }
    }
    
    return true;
  }

  /**
   * Called on every request. Return whatever credentials you want to
   * be passed to getUser() as $credentials.
   */
  public function getCredentials(Request $request) {
    return array('token' => $request->headers->get('X-AUTH-TOKEN'),);
  }

  public function getUser($credentials, UserProviderInterface $userProvider) {
    $apiKey = $credentials['token'];

    if (null === $apiKey) {
      return;
    }

    // if a User object, checkCredentials() is called
    return $userProvider->loadUserByUsername($apiKey);
  }

  public function checkCredentials($credentials, UserInterface $user) {
    // check credentials - e.g. make sure the password is valid
    // no credential check is needed in this case

    // return true to cause authentication success
    return true;
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
    // on success, let the request continue
    return null;
  }

  public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
    $data = array('message' => strtr('Unauthorized actions', $exception->getMessageData())

      // or to translate this message
      // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
    );

    return new JsonResponse($data, Response::HTTP_FORBIDDEN);
  }

  /**
   * Called when authentication is needed, but it's not sent
   */
  public function start(Request $request, AuthenticationException $authException = null) {
    $data = array(// you might translate this message
      'message' => 'Authentication Required');

    return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
  }

  public function supportsRememberMe() {
    return false;
  }
}