<?php

namespace App\Command;

use App\Services\TaliazDoctrine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserPruneTokensCommand extends Command
{
  protected static $defaultName = 'user:prune-tokens';

  /**
   * @var TaliazDoctrine
   */
  protected $TaliazDoctrine;

  public function __construct(?string $name = null, TaliazDoctrine $taliaz_doctrine) {
    parent::__construct($name);

    $this->TaliazDoctrine = $taliaz_doctrine;
  }

  protected function configure() {
    $this->setDescription('Removing old access token from the system');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $tokens = $this->TaliazDoctrine->getAccessTokenRepository()->findAll();

    d($tokens);
  }
}
