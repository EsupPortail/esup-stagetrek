<?php

namespace Application\Controller\Convention\Factory;

use Application\Controller\Convention\ConventionStageController;
use Application\Form\Convention\ConventionStageTeleversementForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\ConventionStage\ConventionStageService;
use Application\Service\ConventionStage\ModeleConventionStageService;
use Application\Service\Renderer\MacroService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class StageControllerFactory
 * @package Application\Controller\Factory
 */
class ConventionStageControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ConventionStageController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConventionStageController
    {
        $controller = new ConventionStageController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ConventionStageService $service */
        $service = $container->get(ServiceManager::class)->get(ConventionStageService::class);
        $controller->setConventionStageService($service);

        /** @var ModeleConventionStageService $service */
        $service = $container->get(ServiceManager::class)->get(ModeleConventionStageService::class);
        $controller->setModeleConventionStageService($service);

        /** @var MacroService $macroService */
        $macroService = $container->get(ServiceManager::class)->get(MacroService::class);
        $controller->setMacroService($macroService);


        /** @var ConventionStageTeleversementForm $form */
        $form = $container->get(FormElementManager::class)->get(ConventionStageTeleversementForm::class);
        $controller->setConventionStageTeleversementForm($form);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);



        return $controller;
    }
}