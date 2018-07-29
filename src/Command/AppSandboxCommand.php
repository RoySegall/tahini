<?php

namespace App\Command;

use App\Entity\Personal\User;
use App\Plugins\Authentication;
use App\Services\TaliazAccessToken;
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

  protected $accessToken;

  public function __construct(?string $name = null, TaliazUser $taliazUser, TaliazAccessToken $accessToken) {
    parent::__construct($name);

    $this->taliazUser = $taliazUser;
    $this->accessToken = $accessToken;
  }

  protected function configure() {
    $this->setDescription('This is a sandbox for testing stuff');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $user = $this->taliazUser->findUserByUsername('admin');
    $this->accessToken->getAccessToken($user);
  }
}
