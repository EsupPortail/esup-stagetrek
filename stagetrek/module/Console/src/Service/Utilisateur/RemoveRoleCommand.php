<?php

namespace Console\Service\Utilisateur;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnicaenUtilisateur\Entity\Db\RoleInterface;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

class RemoveRoleCommand extends Command
{

    protected static $defaultName = 'utilisateur:add-role';

    use UserServiceAwareTrait;


    protected function configure(): void
    {
        $this
            ->setName('utilisateur:remove-role')
            ->setDescription("Retrait d'un role à un utilisateur");
    }

    protected ?SymfonyStyle $io = null;
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
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

        $roles = $user->getRoles()->toArray();
        if(empty($roles)){
            $this->io->info("L'utilisateur n'a pas de rôle de définie");
            return self::SUCCESS;
        }

        usort($roles, function (RoleInterface $r1, RoleInterface $r2) {
            return strcmp($r1->getRoleId(), $r2->getRoleId());
        });
        /** @var RoleInterface $role */
        $role = $this->io->choice(
            "Veuillez choisir le rôle à retirer ",
            $roles
        );

        $this->userService->removeRole($user, $role);
        $this->io->success('Role retiré');

        return Command::SUCCESS;

    }

}
