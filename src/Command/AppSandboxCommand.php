<?php

namespace App\Command;

use App\Entity\Personal\AccessToken;
use App\Entity\Personal\User;
use App\Plugins\Authentication;
use App\Services\TahiniAccessToken;
use App\Services\TahiniUser;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppSandboxCommand extends Command
{
    protected static $defaultName = 'app:sandbox';

  /**
   * @var TahiniUser
   */
    protected $tahiniUser;

  /**
   * @var TahiniAccessToken
   */
    protected $accessToken;

  /**
   * @var ObjectManager
   */
    protected $entityManager;


    public function __construct(
        ?string $name = null,
        TahiniUser $tahiniUser,
        TahiniAccessToken $accessToken,
        \Doctrine\Common\Persistence\ManagerRegistry $registry
    ) {
        parent::__construct($name);

        $this->tahiniUser = $tahiniUser;
        $this->accessToken = $accessToken;
        $this->entityManager = $registry->getManager();
    }

    protected function configure()
    {
        $this->setDescription('This is a sandbox for testing stuff');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->tahiniUser->findUserByUsername('admin');

        $access_token = $this->accessToken
            ->refreshAccessToken('$2y$12$syxxCHNIoZGCBrQEvhwaTuv4mNwgKyCMZFr0cf4I2OQ7Bnb7O8YJO');

        d($access_token);
    }
}
