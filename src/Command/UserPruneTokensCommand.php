<?php

namespace App\Command;

use App\Entity\AccessToken;
use App\Services\TahiniAccessToken;
use App\Services\TahiniDoctrine;
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
   * @var TahiniDoctrine
   */
    protected $TahiniDoctrine;

  /**
   * @var TahiniAccessToken
   */
    protected $TahiniAccessToken;

  /**
   * UserPruneTokensCommand constructor.
   * @param null|string $name
   * @param TahiniDoctrine $tahini_doctrine
   * @param TahiniAccessToken $tahini_access_token
   */
    public function __construct(
        ?string $name = null,
        TahiniDoctrine $tahini_doctrine,
        TahiniAccessToken $tahini_access_token
    ) {
        parent::__construct($name);

        $this->TahiniDoctrine = $tahini_doctrine;
        $this->TahiniAccessToken = $tahini_access_token;
    }

    protected function configure()
    {
        $this->setDescription('Removing old access token from the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

      /** @var AccessToken[] $tokens */
        $tokens = $this->TahiniDoctrine->getAccessTokenRepository()->findAll();
        $counts = 0;
        foreach ($tokens as $token) {
            if (time() > $token->expires) {
                $this->TahiniAccessToken->clearAccessToken($token);
                $io->writeln('The access token for the user ' .
                    $token->user->username . ' has been pruned from tye system');
                $counts++;
            }
        }

        if ($counts === 0) {
            $io->success('No access token were removed');
        } else {
            $message = $counts === 1 ? 'One access token has been pruned' : $counts . ' access tokens were pruned';
            $io->success($message);
        }
    }
}
