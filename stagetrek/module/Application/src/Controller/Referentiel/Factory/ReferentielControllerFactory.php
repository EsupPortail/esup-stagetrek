<?php

namespace Application\Controller\Referentiel\Factory;

use Application\Controller\Referentiel\ReferentielController;
use Application\Service\Referentiel\MultipleReferentielEtudiantsService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ReferentielControllerFactory
 * @package Referentiel\Controller
 * @deprecated Refonte en cours de la gestions  des étudiants
 */
class ReferentielControllerFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Application\Controller\Referentiel\ReferentielController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : ReferentielController
    {
        $controller = new ReferentielController();


        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

//        /** @var MultipleReferentielEtudiantsService $referentielService */
//        $referentielService = $container->get(MultipleReferentielEtudiantsService::class);
//        $controller->setReferentielService($referentielService);

        return $controller;
    }
}