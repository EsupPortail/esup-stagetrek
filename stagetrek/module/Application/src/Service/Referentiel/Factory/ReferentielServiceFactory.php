<?php

namespace Application\Service\Referentiel\Factory;
use Application\Provider\Source\SourceProvider;
use Application\Service\Referentiel\RechercheEtudiant\Interfaces\RechercheEtudiantServiceInterface;
use Application\Service\Referentiel\RechercheEtudiant\RechercheEtudiantLdapService;
use Application\Service\Referentiel\RechercheEtudiant\RechercheEtudiantLocalService;
use Application\Service\Referentiel\ReferentielService;
use Interop\Container\Containerinterface;

class ReferentielServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @return \Application\Service\Referentiel\ReferentielService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container) : ReferentielService
    {
        $service = new ReferentielService();

        /** @var RechercheEtudiantServiceInterface $local */
        $local = $container->get(RechercheEtudiantLocalService::class);
        $service->addReferentielSourceService(SourceProvider::STAGETREK, $local);

        /** @var RechercheEtudiantServiceInterface $ldap */
        $ldap = $container->get(RechercheEtudiantLdapService::class);
        $service->addReferentielSourceService(SourceProvider::LDAP, $ldap);

        return $service;
    }

}