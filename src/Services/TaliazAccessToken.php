<?php

namespace App\Services;

use App\Entity\Personal\AccessToken;
use App\Entity\Personal\User;
use App\Repository\AccessTokenRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Taliaz access token.
 *
 * The service handle all we need with access token - finding the matching user,
 * creating an access token for user and more.
 *
 * @package App\Services
 */
class TaliazAccessToken {

  /**
   * @var TaliazDoctrine
   */
  protected $doctrine;

  /**
   * @var \Doctrine\Common\Persistence\ObjectManager|object
   */
  protected $doctrineManager;

  /**
   * @var TaliazValidator
   */
  protected $taliazValidator;

  /**
   * @var AccessTokenRepository
   */
  protected $accessTokenRepository;

  /**
   * TaliazAccessToken constructor.
   *
   * @param TaliazDoctrine $taliaz_doctrine
   *  The taliaz doctrine service.
   * @param ManagerRegistry $registry
   *  The registry service.
   * @param TaliazValidator $taliaz_validator
   *  The validator service.
   */
  public function __construct(TaliazDoctrine $taliaz_doctrine, ManagerRegistry $registry, TaliazValidator $taliaz_validator, AccessTokenRepository $accessTokenRepository) {
    $this->doctrine = $taliaz_doctrine;
    $this->doctrineManager = $registry->getManager('personal');
    $this->taliazValidator = $taliaz_validator;
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
  public function createAccessToken(\App\Entity\Personal\User $user) : AccessToken {
    $access_token = new AccessToken();

    $access_token->expires = time() + 86400;
    $access_token->refresh_token = $this->generateHash('refresh_token', $user);
    $access_token->access_token = $this->generateHash('access_token', $user);
    $access_token->user = $user;

    $this->taliazValidator->validate($access_token);
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
  public function getAccessToken(\App\Entity\Personal\User $user, bool $unvalid_create_new = false) : AccessToken {
    /** @var AccessToken $access_token */
    if (!$access_token = $this->hasAccessToken($user)) {
      // No access token for the user. Create an access token and return it.
      return $this->createAccessToken($user);
    }

    if ($access_token->expires < time()) {

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
  public function hasAccessToken(\App\Entity\Personal\User $user) {

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
  public function refreshAccessToken(string $refresh_token) : AccessToken {

    /** @var AccessToken[]|null $results */
    if ($results = $this->doctrine->getAccessTokenRepository()->findBy(['refresh_token' => $refresh_token])) {
      $access_token = reset($results);

      // Keep track of the user.
      $user = $access_token->user;
      $access_token->user = NULL;

      // Delete the old access token.
      $this->doctrineManager->remove($access_token);
      $this->doctrineManager->flush();

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
  public function findUserByAccessToken(string $access_token) : User {
    if ($results = $this->doctrine->getAccessTokenRepository()->findBy(['access_token' => $access_token])) {
      /** @var AccessToken $access_token */
      $access_token = reset($results);

      return $access_token->user;
    }

    return new User();
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
  protected function generateHash(string $type, User $user) : string {
    return password_hash($type . $user->id . $user->username . $user->email . time() . microtime(), PASSWORD_BCRYPT, ['cost' => 12]);
  }

}
