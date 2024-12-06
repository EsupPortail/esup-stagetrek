<?php


namespace Application\Form\Contacts\Factory;

use Application\Entity\Db\Contact;
use Application\Form\Contacts\Fieldset\ContactFieldset;
use Application\Form\Misc\Validator\CodeValidator;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Service\Contact\ContactService;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

class ContactFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContactFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContactFieldset
    {
        $fieldset = new ContactFieldset('contact');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);


        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new Contact());

        /**
         * @var ContactService $contactService
         */
        $contactService = $container->get(ServiceManager::class)->get(ContactService::class);
        /** @var CodeValidator $codeValidator */
        $codeValidator = $container->get(ValidatorPluginManager::class)->get(CodeValidator::class);
        $codeValidator->setEntityService($contactService);
        $fieldset->setCodeValidator($codeValidator);

        /** @var LibelleValidator $libelleValidator */
        $libelleValidator = $container->get(ValidatorPluginManager::class)->get(LibelleValidator::class);
        $libelleValidator->setEntityService($contactService);
        $fieldset->setLibelleValidator($libelleValidator);

        return $fieldset;
    }
}