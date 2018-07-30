<?php

namespace App\Command;

use App\Entity\Personal\User;
use App\Services\TaliazUser;
use App\Services\TaliazValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCreateUserCommand extends Command {

  protected static $defaultName = 'user:create-user';

  /**
   * @var TaliazUser
   */
  protected $TaliazUser;

  /**
   * @var TaliazValidator
   */
  protected $TaliazValidator;

  public function __construct(?string $name = null, TaliazUser $taliaz_user, TaliazValidator $taliaz_validator) {
    parent::__construct($name);

    $this->TaliazUser = $taliaz_user;
    $this->TaliazValidator = $taliaz_validator;
  }

  protected function configure() {
    $this->setDescription('Creating a user');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);
    $username = $io->askQuestion(new Question('What is the username'));
    $password = $io->askQuestion(new Question('What is the password'));

    $question = new ChoiceQuestion(
      'What is the user type',
      array('app' => 'Application', 'user' => 'Normal user')
    );

    $type = $this->getHelper('question')->ask($input, $output, $question);

    $user = new User();
    $user->username = $username;
    $user->setPassword($password);
    $user->email = time() . '@example.com';
    $user->type = $type;
    $user->roles = [1];

    if ($error = $this->TaliazValidator->validate($user)) {
      d($error);
      return;
    }

    $this->TaliazUser->createUser($user);
  }
}
