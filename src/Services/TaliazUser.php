<?php

namespace App\Services;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Personal\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Taliaz user service.
 *
 * The service manage all the user actions - creating, updating password.
 *
 * @package App\Services
 */
class TaliazUser {

  /**
   * @var TaliazDoctrine
   */
  protected $doctrine;

  /**
   * @var UserPasswordEncoderInterface
   */
  protected $encoder;

  /**
   * @var TaliazValidator
   */
  protected $taliazValidator;

  /**
   * @var \Doctrine\Common\Persistence\ObjectManager|object
   */
  protected $doctrineManager;

  /**
   * TaliazUser constructor.
   *
   * @param TaliazDoctrine $taliaz_doctrine
   *  The taliaz doctrine service.
   * @param UserPasswordEncoderInterface $encoder
   *  The password encoder.
   * @param TaliazValidator $taliaz_validator
   *  The taliaz validator service.
   * @param ManagerRegistry $registry
   */
  public function __construct(
    TaliazDoctrine $taliaz_doctrine,
    UserPasswordEncoderInterface $encoder,
    TaliazValidator $taliaz_validator,
    ManagerRegistry $registry
  ) {
    $this->doctrine = $taliaz_doctrine;
    $this->encoder = $encoder;
    $this->taliazValidator = $taliaz_validator;
    $this->doctrineManager = $registry->getManager('personal');
  }

  /**
   * Create a hashed password.
   *
   * @param $password
   *  The text to hash.
   *
   * @return string
   */
  public function hashPassword(string $password) : string {
    return $this->encoder->encodePassword(new User(), $password);
  }

  /**
   * Finding the user which the match the username and password.
   *
   * @param string $username
   *  The username.
   * @param string $password
   *  The password, un-hashed.
   * @param string $email
   *  The email of the user.
   *
   * @return User|null
   *  The user object.
   */
  public function findUserByUsername(string $username, string $password = null) {
    $attributes = [
      'username' => $username,
    ];

    if ($password) {
      $attributes['password'] = $password;
    }

    if ($results = $this->doctrine->getUserRepository()->findBy($attributes)) {
      return reset($results);
    }

    return null;
  }

  /**
   * Get user by mail.
   *
   * @param string $email
   *  The email address.
   *
   * @return array
   */
  public function findUserByMail(string $email) {
    $users = $this->doctrine->getUserRepository()->findBy(['email' => $email]);

    if ($user = reset($users)) {
      return $user;
    }

    return null;
  }

  /**
   * Creating the user.
   *
   * @param User $user
   *  The user object.
   *
   * @return User|array
   *  The new user object.
   *
   * @throws \Exception
   */
  public function createUser(User $user) {

    if ($errors = $this->taliazValidator->validate($user, true)) {
      throw $errors;
    }

    // Check the username not exists in the system.
    if ($this->findUserByUsername($user->username)) {
      throw new \Exception('The username already exists');
    }

    // Check the email does not exists.
    if ($this->findUserByMail($user->email)) {
      throw new \Exception('The email already exists');
    }

    $user->setPassword($this->hashPassword($user->getPassword()));

    $this->doctrineManager->persist($user);
    $this->doctrineManager->flush();

    return $user;
  }

  /**
   * Updating the user.
   *
   * @param User $user
   *  The user object.
   *
   * @return User $user
   *  The new user object.
   */
  public function updateUser(User $user) : User {
    $this->doctrineManager->persist($user);
    $this->doctrineManager->flush();

    return $user;
  }

  /**
   * Delete the user.
   *
   * @param User $user
   *  The user object.
   */
  public function deleteUser(User $user) {
    $this->doctrineManager->remove($user);
    $this->doctrineManager->flush();
  }

}
