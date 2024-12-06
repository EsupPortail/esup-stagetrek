<?php

namespace Application\Controller\Terrain\Factory;

use Application\Controller\Terrain\TerrainStageController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Misc\ImportForm;
use Application\Form\TerrainStage\TerrainStageForm;
use Application\Service\Contrainte\ContrainteCursusService;
use Application\Service\TerrainStage\CategorieStageService;
use Application\Service\TerrainStage\TerrainStageService;
use Application\Validator\Import\TerrainStageCsvImportValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class TerrainStageControllerFactory
 * @package Application\Controller\Factory
 */
class TerrainStageControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return TerrainStageController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TerrainStageController
    {
        $controller = new TerrainStageController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /**
         * @var TerrainStageService $terrainStageService
         */
        $terrainStageService = $container->get(ServiceManager::class)->get(TerrainStageService::class);
        $controller->setTerrainStageService($terrainStageService);

        /**
         * @var CategorieStageService $categorieStageService
         */
        $categorieStageService = $container->get(ServiceManager::class)->get(CategorieStageService::class);
        $controller->setCategorieStageService($categorieStageService);

           /**
         * @var TerrainStageForm $terrainStageForm
         */
        $terrainStageForm = $container->get(FormElementManager::class)->get(TerrainStageForm::class);
        $controller->setTerrainStageForm($terrainStageForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        /** @var ContrainteCursusService $contrainteCursusService */
        $contrainteCursusService = $container->get(ServiceManager::class)->get(ContrainteCursusService::class);
        $controller->setContrainteCursusService($contrainteCursusService);

        /** @var ImportForm $importFom */
        $importFom = $container->get(FormElementManager::class)->get(ImportForm::class);
        $controller->setImportForm($importFom);
        
        /** @var TerrainStageCsvImportValidator $importValidator */
        $importValidator = $container->get(ValidatorPluginManager::class)->get(TerrainStageCsvImportValidator::class);
        $controller->setImportValidator($importValidator);

        return $controller;
    }
}