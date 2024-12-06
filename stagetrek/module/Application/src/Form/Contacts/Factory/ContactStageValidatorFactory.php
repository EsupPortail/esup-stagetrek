<?php

namespace Application\Form\Contacts\Factory;

use Application\Form\Contacts\Validator\ContactStageValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ContactStageValidatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContactStageValidator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContactStageValidator
    {
        $validator = new ContactStageValidator($options);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $validator->setObjectManager($entityManager);

        return $validator;
    }
}