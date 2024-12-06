<?php

namespace Application\Service\Referentiel\Factory;

use Application\Service\Referentiel\RechercheEtudiant\RechercheEtudiantLocalService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

class RechercheEtudiantLocalServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return RechercheEtudiantLocalService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): RechercheEtudiantLocalService
    {

        $service = new RechercheEtudiantLocalService();
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);

        return $service;
    }
}