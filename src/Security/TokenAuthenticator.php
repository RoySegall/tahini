<?php

namespace App\Security;

use App\Entity\AccessToken;
use App\Services\TahiniAccessToken;
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
   * @var TahiniAccessToken
   */
    protected $TahiniAccessToken;

  /**
   * @var AccessToken
   */
    protected $token;

  /**
   * TokenAuthenticator constructor.
   * @param TahiniAccessToken $tahini_access_token
   */
    public function __construct(TahiniAccessToken $tahini_access_token)
    {
        $this->TahiniAccessToken = $tahini_access_token;
    }

  /**
   * @var array
   *
   * List of routes which anonymous user are allowed to access.
   */
    protected $allowed_anonymous_paths = [
    '/',
    '/api/user/login',
    '/api/user/refresh',
    ];

  /**
   * @var array
   *
   * List of regex paths.
   */
    protected $allowed_anonymous_paths_regex = [
    '(api\/v2\/job-processes\/)[0-9]'
    ];

  /**
   * {@inheritdoc}
   */
    public function supports(Request $request)
    {
        $path = $request->getRequestUri();

      // Check first if we need to skip the access token auth for paths which
      // anonymous users have access.
        if (in_array($path, $this->allowed_anonymous_paths)) {
            return false;
        }

      // The path does not exists in a simple format. Check the regex format.
        foreach ($this->allowed_anonymous_paths_regex as $allowed_anonymous_paths_regex) {
            if (@preg_match($allowed_anonymous_paths_regex . '/m', $path)) {
                return false;
            }
        }

        return true;
    }

  /**
   * {@inheritdoc}
   */
    public function getCredentials(Request $request)
    {
        return array('token' => $request->headers->get(\App\Services\TahiniAccessToken::ACCESS_TOKEN_HEADER_KEY));
    }

  /**
   * {@inheritdoc}
   */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
      // todo: use the authentication plugin.

        if (empty($credentials['token'])) {
            return null;
        }

        $user = $this->TahiniAccessToken->loadByAccessToken($credentials['token'])->user;

        if (empty($user->id)) {
            return null;
        }

        return $user;
    }

  /**
   * {@inheritdoc}
   */
    public function checkCredentials($credentials, UserInterface $user)
    {
      // todo: use the authentication plugin.
        $this->token = $this->TahiniAccessToken->loadByAccessToken($credentials['token']);

        $user = $this->token->user;

        if (empty($user->id)) {
            return false;
        }

      // Return true to cause authentication success.
        return true;
    }

  /**
   * {@inheritdoc}
   */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
      // On success, let the request continue.
        return null;
    }

  /**
   * {@inheritdoc}
   */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array('message' => strtr('You are not valid. Try again later.', $exception->getMessageData()));

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

  /**
   * {@inheritdoc}
   */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(// you might translate this message
        'message' => 'Authentication Required');

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

  /**
   * {@inheritdoc}
   */
    public function supportsRememberMe()
    {
        return false;
    }
}
