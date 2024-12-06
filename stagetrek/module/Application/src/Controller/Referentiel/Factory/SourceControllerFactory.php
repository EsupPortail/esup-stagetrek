<?php

namespace Application\Controller\Referentiel\Factory;

use Application\Controller\Referentiel\SourceController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Referentiel\SourceForm;
use Application\Service\Referentiel\SourceService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ReferentielControllerFactory
 * @package Referentiel\Controller
 */
class SourceControllerFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SourceController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : SourceController
    {
        $controller = new SourceController();


        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);
        
        /** @var SourceService $sourceService */
        $sourceService = $container->get(SourceService::class);
        $controller->setSourceService($sourceService);

        /** @var SourceForm $sourceForm */
        $sourceForm = $container->get(FormElementManager::class)->get(SourceForm::class);
        $controller->setSourceForm($sourceForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }
}