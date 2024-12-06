<?php

namespace Application\Service\Contrainte\Factory;

use Application\Service\Contrainte\ContrainteCursusEtudiantService;
use Application\Service\Etudiant\EtudiantService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenEtat\Service\EtatInstance\EtatInstanceService;
use UnicaenEtat\Service\EtatType\EtatTypeService;

/**
 * Class ContrainteCursusEtudiantServiceFactory
 * @package Application\Service\ContraintesService\Factory
 */
class ContrainteCursusEtudiantServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Application\Service\Contrainte\ContrainteCursusEtudiantService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @retur ContrainteCursusnEtudiantService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusEtudiantService
    {
        $serviceProvider = new ContrainteCursusEtudiantService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $serviceProvider->setEtudiantService($etudiantService);

        $serviceProvider->setEtatTypeService($container->get(EtatTypeService::class));
        $serviceProvider->setEtatInstanceService($container->get(EtatInstanceService::class));

        return $serviceProvider;
    }
}
