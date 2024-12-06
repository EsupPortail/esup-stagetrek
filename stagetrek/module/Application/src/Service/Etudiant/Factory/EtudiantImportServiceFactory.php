<?php

namespace Application\Service\Etudiant\Factory;

use API\Service\ReferentielEtudiantApiService;
use Application\Service\Etudiant\EtudiantImportService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Misc\CSVService;
use Application\Service\Referentiel\ReferentielService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UnicaenUtilisateur\Service\Role\RoleService;
use UnicaenUtilisateur\Service\User\UserService;

/**
 * Class EtudiantServiceFactory
 * @package Application\Service\Etudiant
 */
class EtudiantImportServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Application\Service\Etudiant\EtudiantImportService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EtudiantImportService
    {
        $service = new EtudiantImportService();

        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);

        $service->setCsvService($container->get(CSVService::class));
        $service->setReferentielService($container->get(ReferentielService::class));
        $service->setEtudiantService($container->get(EtudiantService::class));

        $service->setRoleService($container->get(RoleService::class));
        $service->setUserService($container->get(UserService::class));

        $config = $container->get('Configuration');

        if(!isset($config['StageTrek']['http_client']['api']['referentiel_etudiant']) || $config['StageTrek']['http_client']['api']['referentiel_etudiant'] == []){
            return $service;
        }

        $referentielApiService = $container->get(ReferentielEtudiantApiService::class);
        $service->setReferentielEtudiantApiService($referentielApiService);
        $sourceCode = ($config['StageTrek']['http_client']['api']['referentiel_etudiant']['source_code']) ?? null;
        $service->setReferentielSourceCode($sourceCode);
        $refData = ($config['StageTrek']['http_client']['api']['referentiel_etudiant']['data']) ?? [];

        $service->setReferentielDataConfig($refData);


        return $service;
    }
}