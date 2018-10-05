<?php

namespace App\Command;

use App\Entity\User;
use App\Services\TahiniUser;
use App\Services\TahiniValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCreateUserCommand extends Command
{

    protected static $defaultName = 'user:create-user';

  /**
   * @var TahiniUser
   */
    protected $TahiniUser;

  /**
   * @var TahiniValidator
   */
    protected $TahiniValidator;

  /**
   * UserCreateUserCommand constructor.
   * @param null|string $name
   * @param TahiniUser $tahini_user
   * @param TahiniValidator $tahini_validator
   */
    public function __construct(?string $name = null, TahiniUser $tahini_user, TahiniValidator $tahini_validator)
    {
        parent::__construct($name);

        $this->TahiniUser = $tahini_user;
        $this->TahiniValidator = $tahini_validator;
    }

    protected function configure()
    {
        $this->setDescription('Creating a user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        if ($error = $this->TahiniValidator->validate($user)) {
            d($error);
            return;
        }

        $this->TahiniUser->createUser($user);
    }
}
