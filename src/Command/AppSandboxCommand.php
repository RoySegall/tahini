<?php

namespace App\Command;

use App\Entity\Personal\User;
use App\Plugins\Authentication;
use App\Services\TaliazUser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppSandboxCommand extends Command
{
  protected static $defaultName = 'app:sandbox';

  /**
   * @var TaliazUser
   */
  protected $taliazUser;

  public function __construct(?string $name = null, TaliazUser $taliazUser) {
    parent::__construct($name);

    $this->taliazUser = $taliazUser;
  }

  protected function configure() {
    $this->setDescription('This is a sandbox for testing stuff');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $user = new User();
    $user->username = 'adaamin';
    $user->email = 'roy@foo.aaacom';
    $user->type = 'app';
    $user->roles = [1];
    $user->setPassword('1234');

    $this->taliazUser->createUser($user);
  }
}
