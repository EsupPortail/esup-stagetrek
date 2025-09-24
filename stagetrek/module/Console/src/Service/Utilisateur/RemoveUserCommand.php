<?php

namespace Console\Service\Utilisateur;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

class RemoveUserCommand extends Command
{

    protected static $defaultName = 'utilisateur:add-role';

    use UserServiceAwareTrait;


    protected function configure(): void
    {
        $this
            ->setName('utilisateur:remove-user')
            ->setDescription("Suppression d'un utilisateur");
    }

    protected ?SymfonyStyle $io = null;
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title("Suppresion d'un utilisateur ");

        $username = $this->io->ask('Username');
        $username = (isset($username)) ? trim($username) : null;
        if(!isset($username)||$username==""){
            $this->io->error('username non saisie');
            return Command::FAILURE;
        }
        $user = $this->getUserService()->findByUsername($username);

        if ($user === null) {
            $this->io->error('Utilisateur non trouvé');
            return Command::FAILURE;
        }

        $this->userService->delete($user);
        $this->io->success('Utilisateur supprimé');

        return Command::SUCCESS;

    }

}
