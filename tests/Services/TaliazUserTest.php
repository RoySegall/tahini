<?php

namespace App\Tests\Controller;

use App\Entity\Personal\User;
use App\Tests\TaliazBaseWebTestCase;

class TaliazUserTest extends TaliazBaseWebTestCase {

  /**
   * Testing the user registration.
   */
  public function testUserRegistration() {
    $user = new User();
    $first_validation = $this->getTaliazValidator()->validate($user, true);

    // Making sure the thrown error is what the validator should be.
    try {
      $this->getTaliazUser()->createUser($user);
    } catch (\ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException $e) {
      $this->assertEquals($first_validation, $e);
    }

    $user->username = 'user' . time();
    $user->setPassword('text');
    $user->roles = [1];
    $user->type = 'app';
    $user->email = 'dummy' . time() . '@example.com';

    $user = $this->getTaliazUser()->createUser($user);

    $this->assertNotFalse($user->id);
  }

  /**
   * Testing the hashing of the password.
   */
  public function testHashPassword() {
    $user = new User();
    $user->setPassword('text');
    $this->assertEquals('text', $user->getPassword());
    $this->assertNotEquals($this->getTaliazUser()->hashPassword('text'), 'text');
  }

  /**
   * Testing the finding by username and password.
   */
  public function testFindUserByUsername() {
    $user = $this->createUser();

    // Trying to find a user by the email just to make sure we will fail.
    $this->assertNull($this->getTaliazUser()->findUserByUsername($user->email));

    // Find the user by the name. This time make sure we get the user.
    $found_user = $this->getTaliazUser()->findUserByUsername($user->username);
    $this->assertEquals($found_user, $user);
  }

  /**
   * Testing the finding by email.
   */
  public function testFindUserByEmail() {
    $user = $this->createUser();

    // Trying to find a user the name just to make sure we will fail.
    $this->assertNull($this->getTaliazUser()->findUserByMail($user->username));

    // Get the user by the email.
    $found_user = $this->getTaliazUser()->findUserByMail($user->email);
    $this->assertEquals($user, $found_user);
  }

  /**
   * Testing by the user update.
   */
  public function testUserUpdate() {
    $user = $this->createUser();

    $this->assertNull($this->getTaliazUser()->findUserByMail('pizza@example.com'));

    $user->email = 'pizza@example.com';
    $this->getTaliazUser()->updateUser($user);

    $this->assertNotNull($this->getTaliazUser()->findUserByMail('pizza@example.com'));
  }

  /**
   * Testing the user delete.
   */
  public function testUserDelete() {
    $user = $this->createUser();
    $this->getTaliazUser()->deleteUser($user);
    $this->assertNull($this->getTaliazUser()->findUserByMail($user->email));
  }

}
