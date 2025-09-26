<?php

namespace Application\Service\Referentiel\Factory;

use Application\Entity\Hydrator\EtudiantHydrator;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Groupe\GroupeService;
use Application\Service\Referentiel\LdapEtudiantService;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use UnicaenLdap\Service\People;

class LdapEtudiantServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return LdapEtudiantService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): LdapEtudiantService
    {

        $service = new LdapEtudiantService();

        /** @var People $ldapService */
        $ldapService = $container->get(People::class);
        $service->setLdapPeopleService($ldapService);
        $service->setObjectManager($container->get(EntityManager::class));
        $service->setEtudiantService($container->get(EtudiantService::class));
        $service->setGroupeService($container->get(GroupeService::class));
        /** @var EtudiantHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(EtudiantHydrator::class);
        $service->setEtudiantHydrator($hydrator);

        return $service;
    }
}