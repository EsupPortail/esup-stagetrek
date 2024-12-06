<?php

namespace Application\Service\Contrainte\Factory;

use Application\Service\Contrainte\ContrainteCursusEtudiantService;
use Application\Service\Contrainte\ContrainteCursusService;
use Application\Service\Etudiant\EtudiantService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class ContrainteCursusServiceFactory
 * @package Application\Service\ContraintesService\Factory
 */
class ContrainteCursusServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContrainteCursusService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusService
    {
        $serviceProvider = new ContrainteCursusService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);


        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $serviceProvider->setEtudiantService($etudiantService);

        /** @var ContrainteCursusEtudiantService $contrainteCursusEtudiantService */
        $contrainteCursusEtudiantService = $container->get(ServiceManager::class)->get(ContrainteCursusEtudiantService::class);
        $serviceProvider->setContrainteCursusEtudiantService($contrainteCursusEtudiantService);



        return $serviceProvider;
    }
}
