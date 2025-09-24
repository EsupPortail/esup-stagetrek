<?php

namespace Application\Service\Referentiel\Factory;

use Application\Service\Referentiel\LocalEtudiantService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

class RechercheEtudiantLocalServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return LocalEtudiantService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): LocalEtudiantService
    {

        $service = new LocalEtudiantService();
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);

        return $service;
    }
}