<?php

namespace Console\Service\Utilisateur\Factory;

use Console\Service\Utilisateur\CreateUserCommand;
use Console\Service\Utilisateur\RemoveUserCommand;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Event\RuntimeException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use UnicaenMail\Service\Mail\MailService;
use UnicaenUtilisateur\Service\User\UserService;

class RemoveUserCommandFactory extends Command
{
    /**
     * @param ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): RemoveUserCommand
    {
        $command = new RemoveUserCommand();
        /**
         * @var UserService $userService
         */
        $userService = $container->get(ServiceManager::class)->get(UserService::class);
        $command->setUserService($userService);
        return $command;
    }

}