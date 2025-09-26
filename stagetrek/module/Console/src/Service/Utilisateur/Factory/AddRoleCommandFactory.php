<?php

namespace Console\Service\Utilisateur\Factory;

use Console\Service\Utilisateur\AddRoleCommand;
use Console\Service\Utilisateur\CreateUserCommand;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Event\RuntimeException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use UnicaenMail\Service\Mail\MailService;
use UnicaenUtilisateur\Service\Role\RoleService;
use UnicaenUtilisateur\Service\User\UserService;

class AddRoleCommandFactory extends Command
{
    /**
     * @param ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): AddRoleCommand
    {
        $command = new AddRoleCommand();
        /**
         * @var UserService $userService
         * @var RoleService $roleService
         */
        $userService = $container->get(ServiceManager::class)->get(UserService::class);
        $roleService = $container->get(ServiceManager::class)->get(RoleService::class);
        $command->setUserService($userService);
        $command->setRoleService($roleService);




        return $command;
    }

}