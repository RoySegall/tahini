<?php

namespace App\Services;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Repository\AccessTokenRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tahini access token.
 *
 * The service handle all we need with access token - finding the matching user,
 * creating an access token for user and more.
 *
 * @package App\Services
 */
class TahiniAccessToken
{

  /**
   * The name of the header which holds the access token.
   */
    const ACCESS_TOKEN_HEADER_KEY = 'X-AUTH-TOKEN';

  /**
   * kep the amount of time the access should kept alive.
   */
    const ACCESS_TOKEN_DURATION = 86400;

  /**
   * @var TahiniDoctrine
   */
    protected $doctrine;

  /**
   * @var \Doctrine\Common\Persistence\ObjectManager|object
   */
    protected $doctrineManager;

  /**
   * @var TahiniValidator
   */
    protected $tahiniValidator;

  /**
   * @var AccessTokenRepository
   */
    protected $accessTokenRepository;

  /**
   * TahiniAccessToken constructor.
   *
   * @param TahiniDoctrine $tahini_doctrine
   *  The tahini doctrine service.
   * @param ManagerRegistry $registry
   *  The registry service.
   * @param TahiniValidator $tahini_validator
   *  The validator service.
   * @param AccessTokenRepository $accessTokenRepository
   */
    public function __construct(
        TahiniDoctrine $tahini_doctrine,
        ManagerRegistry $registry,
        TahiniValidator $tahini_validator,
        AccessTokenRepository $accessTokenRepository
    ) {
        $this->doctrine = $tahini_doctrine;
        $this->doctrineManager = $registry->getManager();
        $this->tahiniValidator = $tahini_validator;
        $this->accessTokenRepository = $accessTokenRepository;
    }

  /**
   * Creating an access token.
   *
   * @param User $user
   *  The user object.
   *
   * @return AccessToken
   *  The access token object.
   */
    public function createAccessToken(\App\Entity\User $user) : AccessToken
    {
        $access_token = new AccessToken();

        $access_token->expires = time() + $this->getAccessTokenExpires();
        $access_token->refresh_token = $this->generateHash('refresh_token', $user);
        $access_token->access_token = $this->generateHash('access_token', $user);
        $access_token->user = $user;

        $this->doctrineManager->persist($access_token);
        $this->doctrineManager->flush();

        return $access_token;
    }

  /**
   * Getting an access token for a user. Creates it if not.
   *
   * @param User $user
   *  The user object.
   * @param bool $unvalid_create_new
   *  When the access toke is unvalid we can create a new one.
   *
   * @return AccessToken
   *  The access token object.
   */
    public function getAccessToken(\App\Entity\User $user, bool $unvalid_create_new = false) : AccessToken
    {
      /** @var AccessToken $access_token */
        if (!$access_token = $this->hasAccessToken($user)) {
          // No access token for the user. Create an access token and return it.
            return $this->createAccessToken($user);
        }

        if (time() > $access_token->expires) {
            if ($unvalid_create_new) {
                // Creating a new access token.
                return $this->refreshAccessToken($access_token->refresh_token);
            }

            return new AccessToken();
        }

        return $access_token;
    }

  /**
   * Check if the user has an associated access token.
   *
   * @param User $user
   *  The user object.
   *
   * @return bool
   */
    public function hasAccessToken(\App\Entity\User $user)
    {

        if ($access_token = $this->doctrine->getAccessTokenRepository()->findBy(['user' => $user->id])) {
            return reset($access_token);
        }

        return false;
    }

  /**
   * Refreshing access token.
   *
   * @param string $refresh_token
   *  The refresh token string.
   *
   * @return AccessToken
   */
    public function refreshAccessToken(string $refresh_token) : AccessToken
    {

      /** @var AccessToken[]|null $results */
        if ($results = $this->doctrine->getAccessTokenRepository()->findBy(['refresh_token' => $refresh_token])) {
            $access_token = reset($results);

          // Keep track of the user.
            $user = $access_token->user;
            $this->revokeAccessToken($access_token);

          // Create a new refresh token.
            $new_access_token = $this->createAccessToken($user);

            return $new_access_token;
        }

      // Nothing we found. Now we got
        return new AccessToken();
    }

  /**
   * Get the user object by the access token.
   */
    public function loadByAccessToken(string $access_token) : AccessToken
    {
        if ($results = $this->doctrine->getAccessTokenRepository()->findBy(['access_token' => $access_token])) {
          /** @var AccessToken $access_token */
            $access_token = reset($results);

            if (time() > $access_token->expires) {
                return new AccessToken();
            }

            return $access_token;
        }

        return new AccessToken();
    }

  /**
   * Get the the access token from the request object.
   *
   * @param Request $request
   *  The request service.
   *
   * @return AccessToken
   */
    public function getAccessTokenFromRequest(Request $request) : AccessToken
    {
        return $this->loadByAccessToken($request->headers->get(self::ACCESS_TOKEN_HEADER_KEY));
    }

  /**
   * Revoking the access token from the user.
   */
    public function revokeAccessToken(AccessToken $access_token)
    {
        $access_token->user = null;

      // Delete the old access token.
        $this->doctrineManager->remove($access_token);
        $this->doctrineManager->flush();
    }

  /**
   * Clear the stirng of the access token.
   *
   * @param AccessToken $access_token
   *  The access token object.
   */
    public function clearAccessToken(AccessToken $access_token)
    {
        $access_token->access_token = "";
        $this->doctrineManager->persist($access_token);
        $this->doctrineManager->flush();
    }

  /**
   * Return the duration, in seconds, which the access should kept alive.
   *
   * @todo move to a confiugrable place.
   *
   * @return integer
   */
    public function getAccessTokenExpires()
    {
        return \App\Services\TahiniAccessToken::ACCESS_TOKEN_DURATION;
    }

  /**
   * Generate a hash from random properties of the user object.
   *
   * @param string $type
   *  The type of the token - access token, refresh token.
   * @param User $user
   *  The user object.
   *
   * @return string
   */
    protected function generateHash(string $type, User $user) : string
    {
        return password_hash(
            $type . $user->id . $user->username . $user->email .
            time() . microtime(),
            PASSWORD_BCRYPT,
            ['cost' => 12]
        );
    }
}
