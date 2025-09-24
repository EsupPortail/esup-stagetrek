<?php

namespace Application\Service\Referentiel\Factory;

use Application\Service\Etudiant\EtudiantService;
use Application\Service\Groupe\GroupeService;
use Application\Service\Referentiel\LocalEtudiantService;
use Application\Service\Referentiel\ReferentielDbImportEtudiantService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use UnicaenDbImport\Domain\Source;
use UnicaenDbImport\Entity\Db\Service\ImportLog\ImportLogService;
use UnicaenDbImport\Service\ImportService;
use UnicaenDbImport\Service\SynchroService;

class ReferentielDbImportEtudiantServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return LocalEtudiantService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ReferentielDbImportEtudiantService
    {
        $service = new ReferentielDbImportEtudiantService();
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);
        $service->setEtudiantService($container->get(EtudiantService::class));
        $service->setGroupeService($container->get(GroupeService::class));
        $service->setImportService($container->get(ImportService::class));
        $service->setSynchroService($container->get(SynchroService::class));
        $service->setImportLogService($container->get(ImportLogService::class));


//        $config = $container->get('Config');
//        $sourceConfig = ($config['import']['imports']['etudiant']['source']) ?? [];
//        dd($sourceConfig);
//        $source = Source::fromConfig($sourceConfig);
//        dd($sourceConfig, $source);
        return $service;
    }
}