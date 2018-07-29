<?php

namespace App\Services;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Personal\AccessToken;
use App\Entity\Personal\User;
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
   * TaliazAccessToken constructor.
   *
   * @param TaliazDoctrine $taliaz_doctrine
   *  The taliaz doctrine service.
   * @param ManagerRegistry $registry
   */
  public function __construct(TaliazDoctrine $taliaz_doctrine, ManagerRegistry $registry) {
    $this->doctrine = $taliaz_doctrine;
    $this->doctrineManager = $registry->getManager('personal');
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
  public function getAccessToken(\App\Entity\Personal\User $user) : AccessToken {
  }

  /**
   * Check if the user has an associated access token.
   *
   * @param User $user
   *  The user object.
   *
   * @return bool
   */
  public function hasAccessToken(\App\Entity\Personal\User $user) : bool {
    return true;
  }

}
