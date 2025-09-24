<?php

namespace Console\Service\Utilisateur\Factory;

use Console\Service\Utilisateur\RemoveRoleCommand;
use Console\Service\Utilisateur\RemoveUserCommand;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use UnicaenUtilisateur\Service\User\UserService;

class RemoveRoleCommandFactory extends Command
{
    /**
     * @param ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): RemoveRoleCommand
    {
        $command = new RemoveRoleCommand();
        /**
         * @var UserService $userService
         */
        $userService = $container->get(ServiceManager::class)->get(UserService::class);
        $command->setUserService($userService);
        return $command;
    }

}