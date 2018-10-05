<?php

namespace App\Command;

use App\Services\TaliazAccessToken;
use App\Services\TaliazUser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserGenerateAccessTokenCommand extends Command {
  protected static $defaultName = 'user:generate-access-token';

  /**
   * @var TaliazAccessToken
   */
  protected $TaliazAccessToken;

  /**
   * @var TaliazUser
   */
  protected $TaliazUser;

  public function __construct(?string $name = null, TaliazUser $taliaz_user, TaliazAccessToken $taliaz_access_token) {
    parent::__construct($name);

    $this->TaliazUser = $taliaz_user;
    $this->TaliazAccessToken = $taliaz_access_token;
  }

  protected function configure() {
    $this->setDescription('Generate an access token');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);

    $username = $io->askQuestion(new Question('Enter the user name'));

    if (!$user = $this->TaliazUser->findUserByUsername($username)) {
      $io->error('There is no user with that name');
      return;
    }

    $access_token = $this->TaliazAccessToken->getAccessToken($user, true);

    d($access_token);
  }
}