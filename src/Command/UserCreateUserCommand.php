<?php

namespace App\Command;

use App\Entity\Personal\User;
use App\Services\TaliazUser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCreateUserCommand extends Command {

  protected static $defaultName = 'user:create-user';

  /**
   * @var TaliazUser
   */
  protected $TaliazUser;

  public function __construct(?string $name = null, TaliazUser $taliaz_user) {
    parent::__construct($name);

    $this->TaliazUser = $taliaz_user;
  }

  protected function configure() {
    $this->setDescription('Creating a user');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);
    $username = $io->askQuestion(new Question('What is the username'));
    $password = $io->askQuestion(new Question('What is the password'));
    $type = $io->askQuestion(new Question('What the type of the user - app/user'));


    $user = new User();
    $user->username = $username;
    $user->setPassword($password);
    $user->email = time() . '@example.com';
    $user->type = $type;
    $user->roles = [1];
    $this->TaliazUser->createUser($user);
  }
}
