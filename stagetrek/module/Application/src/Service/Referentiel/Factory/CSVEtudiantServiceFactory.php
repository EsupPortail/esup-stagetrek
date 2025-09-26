<?php

namespace Application\Service\Referentiel\Factory;

use Application\Service\Etudiant\EtudiantService;
use Application\Service\Groupe\GroupeService;
use Application\Service\Misc\CSVService;
use Application\Service\Referentiel\CsvEtudiantService;
use Application\Service\Referentiel\LocalEtudiantService;
use Application\Service\Referentiel\ReferentielDbImportEtudiantService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

class CSVEtudiantServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return LocalEtudiantService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CsvEtudiantService
    {

        $service = new CsvEtudiantService();
        $service->setObjectManager($container->get(EntityManager::class));
        $service->setEtudiantService($container->get(EtudiantService::class));
        $service->setCsvService($container->get(CSVService::class));
        $service->setGroupeService($container->get(GroupeService::class));

        return $service;
    }
}