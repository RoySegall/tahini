<?php

namespace App\Command;

use App\Plugins\Authentication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppSandboxCommand extends Command
{
  protected static $defaultName = 'app:sandbox';

  /**
   * @var Authentication
   */
  protected $authentication;

  public function __construct(?string $name = null, Authentication $authentication) {
    parent::__construct($name);

    $this->authentication = $authentication;
  }

  protected function configure() {
    $this->setDescription('This is a sandbox for testing stuff');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $foo = $this->authentication->negotiate();
    d($foo);
  }
}
