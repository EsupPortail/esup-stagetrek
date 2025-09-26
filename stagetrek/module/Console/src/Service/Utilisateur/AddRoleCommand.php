<?php

namespace Console\Service\Utilisateur;

use Application\Provider\Misc\TypeAuthentificationProvider;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnicaenMail\Service\Mail\MailServiceAwareTrait;
use UnicaenUtilisateur\Entity\Db\Role;
use UnicaenUtilisateur\Entity\Db\User;
use UnicaenUtilisateur\Exception\RuntimeException;
use UnicaenUtilisateur\Service\Role\RoleServiceAwareTrait;
use UnicaenUtilisateur\Service\User\UserService;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

class AddRoleCommand extends Command
{
    protected static $defaultName = 'utilisateur:add-role';

    use UserServiceAwareTrait;
    use RoleServiceAwareTrait;

    protected function configure() : static
    {
        $this->setDescription("Attribut un role à un utilisateur");
        return $this;
    }
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        try{
            $this->io = new SymfonyStyle($input, $output);
        }
        catch (Exception $e){
            $this->io->error($e->getMessage());
            exit(-1);
        }
    }

    protected ?SymfonyStyle $io = null;


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->io = new SymfonyStyle($input, $output);
        try {
            $this->io->title("Attribution d'un rôle à un utilisateur");

            $username = $this->io->ask('Username');
            $username = (isset($username)) ? trim($username) : null;
            if(!isset($username)||$username==""){
                $this->io->error('username non saisie');
                return Command::FAILURE;
            }
            $user = $this->getUserService()->findByUsername($username);
            if (!isset($user)) {
                $this->io->error("L'utilisateur ".$username." n'as pas été trouvée");
                return Command::FAILURE;
            }

            $roles = $this->getRoleService()->getRepo()->findBy(['auto' => false],['roleId' => 'asc']);
            //Inutiles de proposer des roles que l'utilisateur à déjà
            $roles =  array_filter($roles, function (Role $r) use ($user){
                return !$user->hasRole($r);
            });
            sort($roles);

            if(empty($roles)){
                $this->io->info("Aucun nouveau rôle n'est disponible pour ".$username);
                return self::SUCCESS;
            }
            /** @var \UnicaenUtilisateur\Entity\Db\RoleInterface $role */
            $role = $this->io->choice(
                "Veuillez choisir le rôle à attribuer ",
                $roles
            );
            $this->getUserService()->addRole($user, $role);
            $this->io->info("Le rôle ".$role->getLibelle()." a été attribuée à ".$username);


        }
        catch (Exception $e){
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    protected array $authentificationServices = [];

    public function getAuthentificationServices(): array
    {
        return $this->authentificationServices;
    }

    public function setAuthentificationServices(array $authentificationServices): static
    {
        $this->authentificationServices = $authentificationServices;
        return $this;
    }

}
