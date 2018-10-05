<?php

namespace App\Tests;

use App\Entity\User;
use App\Plugins\Authentication;
use App\Services\TahiniAccessToken;
use App\Services\TahiniDoctrine;
use App\Services\TahiniUser;
use App\Services\TahiniValidator;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base test class.
 *
 * @package App\Tests
 */
class TahiniBaseWebTestCase extends WebTestCase {

  public function setUp() {
    parent::setUp();

    // Booting up the kernal.
    self::bootKernel();
  }

  /**
   * Create a user.
   *
   * @param bool $create_user
   *  Determine if we need to create a user.
   *
   * @return User
   *  The user object.
   *
   * @throws \Exception
   */
  public function createUser(bool $create_user = true) : User {
    $user = new User();
    $user->username = 'user' . microtime();
    $user->setPassword('text');
    $user->roles = [1];
    $user->type = 'app';
    $user->email = 'dummy' . microtime() . '@example.com';

    if ($create_user) {
      $this->getTahiniUser()->createUser($user);
    }

    return $user;
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
   * Get the validator service.
   *
   * @return TahiniValidator
   */
  protected function getTahiniValidator(): TahiniValidator {
    return $this->getContainer()->get('App\Services\TahiniValidator');
  }

  /**
   * Get the doctrine service.
   *
   * @return TahiniDoctrine
   */
  protected function getTahiniDoctrine() : TahiniDoctrine {
    return $this->getContainer()->get('App\Services\TahiniDoctrine');
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
   * @return TahiniUser
   */
  protected function getTahiniUser() : TahiniUser {
    return $this->getContainer()->get('App\Services\TahiniUser');
  }

  /**
   * Get the access token service.
   *
   * @return TahiniAccessToken
   */
  public function getTahiniAccessToken() : TahiniAccessToken {
    return $this->getContainer()->get('App\Services\TahiniAccessToken');
  }

  /**
   * Get the request object.
   *
   * @return Request
   */
  protected function &getRequest() : Request {
    static $request;

    if ($request) {
      return $request;
    }

    $request = new Request();

    return $request;
  }

}
