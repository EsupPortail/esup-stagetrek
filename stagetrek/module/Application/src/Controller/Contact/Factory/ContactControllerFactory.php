<?php

namespace Application\Controller\Contact\Factory;

use Application\Controller\Contact\ContactController;
use Application\Form\Contacts\ContactForm;
use Application\Form\Contacts\ContactRechercheForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Misc\ImportForm;
use Application\Service\Contact\ContactService;
use Application\Validator\Import\ContactCsvImportValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class ContactControllerFactory
 * @package Application\Controller\Factory
 */
class ContactControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContactController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContactController
    {
        $controller = new ContactController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ContactService $contactService */
        $contactService = $container->get(ServiceManager::class)->get(ContactService::class);
        $controller->setContactService($contactService);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        /** @var ContactForm $contactForm */
        $contactForm = $container->get(FormElementManager::class)->get(ContactForm::class);
        $controller->setContactForm($contactForm);

        /** @var ContactRechercheForm $contactRechecheForm */
        $contactRechecheForm = $container->get(FormElementManager::class)->get(ContactRechercheForm::class);
        $controller->setContactRechercheForm($contactRechecheForm);

        /** @var ImportForm $importFom */
        $importFom = $container->get(FormElementManager::class)->get(ImportForm::class);
        $controller->setImportForm($importFom);

        /** @var ContactCsvImportValidator $importValidator */
        $importValidator = $container->get(ValidatorPluginManager::class)->get(ContactCsvImportValidator::class);
        $controller->setImportValidator($importValidator);

        return $controller;
    }
}