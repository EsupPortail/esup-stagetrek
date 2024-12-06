<?php

namespace Application\Service\Referentiel\Factory;

use Application\Service\Referentiel\RechercheEtudiant\RechercheEtudiantLdapService;
use Interop\Container\ContainerInterface;
use UnicaenLdap\Service\People;

class RechercheEtudiantLdapServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return RechercheEtudiantLdapService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): RechercheEtudiantLdapService
    {

        $service = new RechercheEtudiantLdapService();

        /** @var People $ldapService */
        $ldapService = $container->get(People::class);
        $service->setLdapPeopleService($ldapService);

        return $service;
    }
}