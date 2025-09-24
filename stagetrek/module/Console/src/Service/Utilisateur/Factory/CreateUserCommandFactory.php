<?php

namespace Console\Service\Utilisateur\Factory;

use Console\Service\Utilisateur\CreateUserCommand;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Event\RuntimeException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use UnicaenMail\Service\Mail\MailService;
use UnicaenUtilisateur\Service\User\UserService;

class CreateUserCommandFactory extends Command
{
    /**
     * @param ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CreateUserCommand
    {
        $command = new CreateUserCommand();
        /**
         * @var UserService $userService
         */
        $userService = $container->get(ServiceManager::class)->get(UserService::class);
        $command->setUserService($userService);


        $config = $container->get('Config');
        $authServices = isset($config['unicaen-auth']['authentications_services']) ? $config['unicaen-auth']['authentications_services'] : [];
        if(!is_array($authServices) || empty($authServices)){
            throw new RuntimeException("La clé de configuration 'unicaen-auth > authentications_services' n'est pas correctement définie");
        }
        $command->setAuthentificationServices($authServices);

        $config = $container->get('Config');
        $appname = $config['unicaen-app']['app_infos']['nom'];
        $uriHost = ($config['console-cli']['uri-host']);
        $uriScheme = ($config['console-cli']['uri-scheme']);
        $command->setAppname($appname);
        $command->setUriHost($uriHost);
        $command->setUriScheme($uriScheme);

        $mailService = $container->get(MailService::class);
        $command->setMailService($mailService);

        return $command;
    }

}