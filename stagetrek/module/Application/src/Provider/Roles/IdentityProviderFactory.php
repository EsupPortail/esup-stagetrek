<?php

namespace Application\Provider\Roles;
use Application\Service\Etudiant\EtudiantService;
use Interop\Container\ContainerInterface;
use UnicaenUtilisateur\Service\Role\RoleService;
use UnicaenUtilisateur\Service\User\UserService;

class IdentityProviderFactory
{
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container) : IdentityProvider
    {
        $service = new IdentityProvider();
        $service->setRoleService($container->get(RoleService::class));
        $service->setUserService($container->get(UserService::class));

        $etudiantService = $container->get(EtudiantService::class);
        $service->setEtudiantService($etudiantService);

        $config = $container->get('Configuration');
        if(isset($config['unicaen-auth']['default_users'])){
            $defaultUsers = $config['unicaen-auth']['default_users'];
            $service->setDefaultUsers($defaultUsers);
        }
        return $service;
    }
}