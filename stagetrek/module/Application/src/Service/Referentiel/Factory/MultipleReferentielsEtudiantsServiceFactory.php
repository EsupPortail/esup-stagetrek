<?php

namespace Application\Service\Referentiel\Factory;
use Application\Entity\Db\Source;
use Application\Service\Referentiel\Interfaces\RechercheEtudiantServiceInterface;
use Application\Service\Referentiel\LdapEtudiantService;
use Application\Service\Referentiel\LocalEtudiantService;
use Application\Service\Referentiel\MultipleReferentielEtudiantsService;
use Interop\Container\Containerinterface;

class MultipleReferentielsEtudiantsServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @return \Application\Service\Referentiel\MultipleReferentielEtudiantsService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container) : MultipleReferentielEtudiantsService
    {
        return new MultipleReferentielEtudiantsService();

    }

}