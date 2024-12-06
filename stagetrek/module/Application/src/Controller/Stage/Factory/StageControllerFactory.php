<?php

namespace Application\Controller\Stage\Factory;

use Application\Controller\Stage\StageController;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\Stage\StageService;
use Application\Validator\Actions\StageValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class StageControllerFactory
 * @package Application\Controller\Factory
 */
class StageControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return StageController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StageController
    {
        $controller = new StageController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var StageService $stageService */
        $stageService = $container->get(ServiceManager::class)->get(StageService::class);
        $controller->setStageService($stageService);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);


        /** @var \Application\Validator\Actions\StageValidator $validator */
        $validator = $container->get(ValidatorPluginManager::class)->get(StageValidator::class);
        $controller->setStageValidator($validator);


        return $controller;
    }
}