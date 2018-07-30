<?php

namespace App\Tests;

use App\Entity\Personal\User;
use App\Plugins\Authentication;
use App\Services\TaliazAccessToken;
use App\Services\TaliazDoctrine;
use App\Services\TaliazOldProcessor;
use App\Services\TaliazUser;
use App\Services\TaliazValidator;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Base test class.
 *
 * @package App\Tests
 */
class TaliazBaseWebTestCase extends WebTestCase {

  public function setUp() {
    parent::setUp();

    // Booting up the kernal.
    self::bootKernel();
  }

  /**
   * Create a user.
   *
   * @return User
   *  The user object.
   *
   * @throws \Exception
   */
  public function createUser() : User {
    $user = new User();
    $user->username = 'user' . time() . microtime();
    $user->setPassword('text');
    $user->roles = [1];
    $user->type = 'app';
    $user->email = 'dummy' . time() . microtime() . '@example.com';
    return $this->getTaliazUser()->createUser($user);
  }

  /**
   * Get the container service.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected function getContainer() : ContainerInterface {
    return self::$kernel->getContainer();
  }

  /**
   * Get the doctrine service.
   *
   * @return \Symfony\Bridge\Doctrine\ManagerRegistry
   */
  protected function getDoctrine() : ManagerRegistry {
    return $this->getContainer()->get('doctrine');
  }

  /**
   * Get taliaz old processor.
   *
   * @return TaliazOldProcessor
   */
  protected function getTaliazOldProcessor() : TaliazOldProcessor {
    return $this->getContainer()->get('App\Services\TaliazOldProcessor');
  }

  /**
   * Get the validator service.
   *
   * @return TaliazValidator
   */
  protected function getTaliazValidator(): TaliazValidator {
    return $this->getContainer()->get('App\Services\TaliazValidator');
  }

  /**
   * Get the doctrine service.
   *
   * @return TaliazDoctrine
   */
  protected function getTaliazDoctrine() : TaliazDoctrine {
    return $this->getContainer()->get('App\Services\TaliazDoctrine');
  }

  /**
   * Get the authentication service.
   *
   * @return Authentication
   *  The authentication service.
   */
  public function getAuthenticationService() : Authentication {
    return $this->getContainer()->get('App\Plugins\Authentication');
  }

  /**
   * Get the user service.
   *
   * @return TaliazUser
   */
  protected function getTaliazUser() : TaliazUser {
    return $this->getContainer()->get('App\Services\TaliazUser');
  }

  /**
   * Get the access token service.
   *
   * @return TaliazAccessToken
   */
  public function getTaliazAccessToken() : TaliazAccessToken {
    return $this->getContainer()->get('App\Services\TaliazAccessToken');
  }

}
