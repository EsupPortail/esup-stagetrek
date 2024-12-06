<?php

namespace Application\Controller\Contact\Factory;

use Application\Controller\Contact\ContactStageController;
use Application\Form\Contacts\ContactStageForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\Contact\ContactStageService;
use Doctrine\ORM\EntityManager;
use Evenement\Service\MailAuto\MailAutoStageDebutValidation;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class ContactStageControllerFactory
 * @package Application\Controller\Factory
 */
class ContactStageControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContactStageController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContactStageController
    {
        $controller = new ContactStageController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        /** @var ContactStageService $contactStageService */
        $contactStageService = $container->get(ServiceManager::class)->get(ContactStageService::class);
        $controller->setContactStageService($contactStageService);

        /** @var ContactStageForm $contactStageForm */
        $contactStageForm = $container->get(FormElementManager::class)->get(ContactStageForm::class);
        $controller->setContactStageForm($contactStageForm);

        /** @var MailAutoStageDebutValidation $mailAutoService */
        $mailAutoService = $container->get(ServiceManager::class)->get(MailAutoStageDebutValidation::class);
        $controller->setMailAutoStageDebutValidationService($mailAutoService);

        return $controller;
    }
}