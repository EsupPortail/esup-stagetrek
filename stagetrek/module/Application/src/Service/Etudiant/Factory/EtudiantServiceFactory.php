<?php

namespace Application\Service\Etudiant\Factory;

use Application\Service\Etudiant\EtudiantService;
use Application\Service\Misc\CSVService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenEtat\Service\EtatInstance\EtatInstanceService;
use UnicaenEtat\Service\EtatType\EtatTypeService;
use UnicaenUtilisateur\Service\Role\RoleService;
use UnicaenUtilisateur\Service\User\UserService;

/**
 * Class EtudiantServiceFactory
 * @package Application\Service\Etudiant
 */
class EtudiantServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EtudiantService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EtudiantService
    {
        $serviceProvider = new EtudiantService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var CSVService $csvService */
        $csvService = $container->get(ServiceManager::class)->get(CSVService::class);
        $serviceProvider->setCsvService($csvService);

        /** @var RoleService $serviceRole */
        $serviceRole = $container->get(ServiceManager::class)->get(RoleService::class);
        $serviceProvider->setRoleService($serviceRole);

        /** @var UserService $userService */
        $userService = $container->get(ServiceManager::class)->get(UserService::class);
        $serviceProvider->setUserService($userService);

        $serviceProvider->setEtatTypeService($container->get(EtatTypeService::class));
        $serviceProvider->setEtatInstanceService($container->get(EtatInstanceService::class));

        return $serviceProvider;
    }
}