<?php

namespace BddAdmin\Data\Factory;

use BddAdmin\Data\UnicaenUtilisateurDataProvider;
use Interop\Container\Containerinterface;
use UnicaenUtilisateur\Service\User\UserService;

class UnicaenUtilisateurDataProviderFactory
{
    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container) : UnicaenUtilisateurDataProvider
    {
        $service = new UnicaenUtilisateurDataProvider();
        /** @var UserService $userService */
        $userService = $container->get(UserService::class);
        $service->setUserService($userService);


        $config = $container->get('config');
        $defaultUser = ($config['unicaen-bddadmin']['data']['unicaen-utilisateur']) ?? [];
        $service->setDefaultUser($defaultUser);
        return $service;
    }


}