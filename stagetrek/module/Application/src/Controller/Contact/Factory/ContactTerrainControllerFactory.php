<?php

namespace Application\Controller\Contact\Factory;

use Application\Controller\Contact\ContactTerrainController;
use Application\Form\Contacts\ContactTerrainForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Misc\ImportForm;
use Application\Service\Contact\ContactTerrainService;
use Application\Validator\Import\ContactTerrainCsvImportValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class ContactTerrainControllerFactory
 * @package Application\Controller\Factory
 */
class ContactTerrainControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContactTerrainController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContactTerrainController
    {
        $controller = new ContactTerrainController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        /** @var ContactTerrainService $contactTerrainService */
        $contactTerrainService = $container->get(ServiceManager::class)->get(ContactTerrainService::class);
        $controller->setContactTerrainService($contactTerrainService);

        /** @var ContactTerrainForm $contactTerrainForm */
        $contactTerrainForm = $container->get(FormElementManager::class)->get(ContactTerrainForm::class);
        $controller->setContactTerrainForm($contactTerrainForm);

        /** @var ImportForm $importFom */
        $importFom = $container->get(FormElementManager::class)->get(ImportForm::class);
        $controller->setImportForm($importFom);

        /** @var ContactTerrainCsvImportValidator $importValidator */
        $importValidator = $container->get(ValidatorPluginManager::class)->get(ContactTerrainCsvImportValidator::class);
        $controller->setImportValidator($importValidator);


        return $controller;
    }
}