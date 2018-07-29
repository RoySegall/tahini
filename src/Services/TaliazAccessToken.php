<?php

namespace App\Services;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Main\AccessTokenTable;
use App\Entity\Main\Foo;
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
  public function createAccessToken(\App\Entity\Personal\User $user) : AccessTokenTable {
    $access_token = new Foo();
    $this->taliazValidator->validate($access_token, true);
    $this->doctrineManager->getRepository($access_token);
    $this->doctrineManager->flush();
    return $access_token;
  }

  /**
   * Getting an access token for a user.
   *
   * @param User $user
   *  The user object.
   *
   * @return AccessToken
   *  The access token object.
   */
  public function getAccessToken(\App\Entity\Personal\User $user) : AccessTokenTable {
    if (!$access_token = $this->hasAccessToken($user)) {
      // No access token for the user. Create an access token and return it.
      return $this->createAccessToken($user);
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
   * Generate a hash from random properties of the user object.
   *
   * @param string $type
   *  The type of the token - access token, refresh token.
   * @param User $username
   *  The user object.
   *
   * @return string
   */
  protected function generateHash(string $type, User $username) : string {
    return password_hash($type . $username->id . $username->username . $username->email, PASSWORD_BCRYPT, ['cost' => 12]);
  }

}
