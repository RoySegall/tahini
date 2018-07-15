<?php

namespace App\Tests;

use App\Services\TaliazOldProcessor;
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

}
