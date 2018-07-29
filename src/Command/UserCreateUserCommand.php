<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCreateUserCommand extends Command {

  protected static $defaultName = 'user:create-user';

  protected function configure() {
    $this->setDescription('Creating a user');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);
    $username = $io->askQuestion(new Question('What is the username'));
    $password = $io->askQuestion(new Question('What is the password'));

    d([$username, $password]);
  }
}
