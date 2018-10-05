<?php

namespace App\Command;

use App\Services\TahiniAccessToken;
use App\Services\TahiniUser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserGenerateAccessTokenCommand extends Command
{

    protected static $defaultName = 'user:generate-access-token';

  /**
   * @var TahiniAccessToken
   */
    protected $TahiniAccessToken;

  /**
   * @var TahiniUser
   */
    protected $TahiniUser;

    public function __construct(?string $name = null, TahiniUser $tahini_user, TahiniAccessToken $tahini_access_token)
    {
        parent::__construct($name);

        $this->TahiniUser = $tahini_user;
        $this->TahiniAccessToken = $tahini_access_token;
    }

    protected function configure()
    {
        $this->setDescription('Generate an access token');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $username = $io->askQuestion(new Question('Enter the user name'));

        if (!$user = $this->TahiniUser->findUserByUsername($username)) {
            $io->error('There is no user with that name');
            return;
        }

        $access_token = $this->TahiniAccessToken->getAccessToken($user, true);

        d($access_token);
    }
}
