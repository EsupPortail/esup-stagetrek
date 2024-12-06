<?php

namespace Application\Controller\Terrain\Factory;

use Application\Controller\Terrain\CategorieStageController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Misc\ImportForm;
use Application\Form\TerrainStage\CategorieStageForm;
use Application\Service\TerrainStage\CategorieStageService;
use Application\Validator\Import\CategorieStageCsvImportValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class CategorieStageControllerFactory
 * @package Application\Controller\Factory
 */
class CategorieStageControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CategorieStageController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CategorieStageController
    {
        $controller = new CategorieStageController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /**
         * @var CategorieStageService $categorieStageService
         */
        $categorieStageService = $container->get(ServiceManager::class)->get(CategorieStageService::class);
        $controller->setCategorieStageService($categorieStageService);

        /**
         * @var CategorieStageForm $categorieStageForm
         */
        $categorieStageForm = $container->get(FormElementManager::class)->get(CategorieStageForm::class);
        $controller->setCategorieStageForm($categorieStageForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);
        /** @var ImportForm $importFom */
        $importFom = $container->get(FormElementManager::class)->get(ImportForm::class);
        $controller->setImportForm($importFom);

        /** @var CategorieStageCsvImportValidator $importValidator */
        $importValidator = $container->get(ValidatorPluginManager::class)->get(CategorieStageCsvImportValidator::class);
        $controller->setImportValidator($importValidator);

        return $controller;
    }
}