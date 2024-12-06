<?php

namespace Application\Controller\Stage\Factory;

use Application\Controller\Stage\ValidationStageController;
use Application\Form\Stages\ValidationStageForm;
use Application\Service\Stage\StageService;
use Application\Service\Stage\ValidationStageService;
use Application\Validator\Actions\ValidationStageValidator;
use Doctrine\ORM\EntityManager;
use Evenement\Service\MailAuto\MailAutoStageValidationEffectueEvenementService;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class StageControllerFactory
 * @package Application\Controller\Factory
 */
class ValidationStageControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Application\Controller\Stage\ValidationStageController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ValidationStageController
    {
        $controller = new ValidationStageController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var StageService $stageService */
        $stageService = $container->get(ServiceManager::class)->get(StageService::class);
        $controller->setStageService($stageService);

        /** @var ValidationStageService $validationStageService */
        $validationStageService = $container->get(ServiceManager::class)->get(ValidationStageService::class);
        $controller->setValidationStageService($validationStageService);

        /** @var \Application\Validator\Actions\ValidationStageValidator $validator */
        $validator = $container->get(ValidatorPluginManager::class)->get(ValidationStageValidator::class);
        $controller->setValidationStageValidator($validator);

        /** @var ValidationStageForm $validationStageForm */
        $validationStageForm = $container->get(FormElementManager::class)->get(ValidationStageForm::class);
        $controller->setValidationStageForm($validationStageForm);

        /** @var MailAutoStageValidationEffectueEvenementService $mailAutoService */
        $mailAutoService = $container->get(ServiceManager::class)->get(MailAutoStageValidationEffectueEvenementService::class);
        $controller->setMailAutoStageValidationEffectueEvenementService($mailAutoService);


        return $controller;
    }
}