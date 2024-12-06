<?php

namespace Application\Controller\Convention\Factory;

use Application\Controller\Convention\ModeleConventionController;
use Application\Form\Convention\ModeleConventionStageForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\ConventionStage\ModeleConventionStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class StageControllerFactory
 * @package Application\Controller\Factory
 */
class ModeleConventionControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ModeleConventionController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ModeleConventionController
    {
        $controller = new ModeleConventionController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ModeleConventionStageService $service */
        $service = $container->get(ServiceManager::class)->get(ModeleConventionStageService::class);
        $controller->setModeleConventionStageService($service);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        /** @var ModeleConventionStageForm $form */
        $form = $container->get(FormElementManager::class)->get(ModeleConventionStageForm::class);
        $controller->setModeleConventionStageForm($form);

        return $controller;
    }
}