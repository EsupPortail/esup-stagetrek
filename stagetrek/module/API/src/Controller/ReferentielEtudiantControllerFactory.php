<?php

namespace API\Controller;

use Application\Service\Referentiel\RechercheEtudiant\RechercheEtudiantLdapService;
use Doctrine\ORM\EntityManager;
use Exception;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ReferentielEtudiantControllerFactory implements FactoryInterface
{
    /**
     * Cretae controller
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ReferentielEtudiantController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ReferentielEtudiantController
    {

        $controller = new ReferentielEtudiantController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        $config = $container->get('Configuration');

        if(!isset($config['StageTrek']['http_client']['api']['referentiel_etudiant']) ||
            empty($config['StageTrek']['http_client']['api']['referentiel_etudiant'])
            ||!isset($config['unicaen-ldap']['host'])
            || $config['unicaen-ldap']['host'] ==""
        ){
            return $controller;
        }

        /** @var RechercheEtudiantLdapService $ldapService */
        $ldapService = $container->get(RechercheEtudiantLdapService::class);
        $controller->setLdapService($ldapService);

        $token = ($config['StageTrek']['http_client']['api']['referentiel_etudiant']['token']) ?? "";
        $controller->setUrlToken($token);

        $data = ($config['StageTrek']['http_client']['api']['referentiel_etudiant']['data']) ?? [];
        $controller->setDataConfig($data);

        return $controller;
    }
}