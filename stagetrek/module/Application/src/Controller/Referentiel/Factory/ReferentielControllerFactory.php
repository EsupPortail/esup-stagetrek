<?php

namespace Application\Controller\Referentiel\Factory;

use Application\Controller\Referentiel\ReferentielController;
use Application\Service\Referentiel\ReferentielService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ReferentielControllerFactory
 * @package Referentiel\Controller
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

        /** @var ReferentielService $referentielService */
        $referentielService = $container->get(ReferentielService::class);
        $controller->setReferentielService($referentielService);

        return $controller;
    }
}