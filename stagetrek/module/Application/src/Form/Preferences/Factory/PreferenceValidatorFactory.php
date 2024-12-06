<?php

namespace Application\Form\Preferences\Factory;

use Application\Form\Preferences\Validator\PreferenceValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class PreferenceValidatorFactory
 * @package Application\Form\Factory\Validator
 */
class PreferenceValidatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PreferenceValidator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PreferenceValidator
    {
        $validator = new PreferenceValidator($options);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $validator->setObjectManager($entityManager);

        return $validator;
    }
}