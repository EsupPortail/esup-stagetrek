<?php

namespace Application\Form\Etudiant\Factory;

use Application\Form\Etudiant\Validator\DisponibiliteValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class DisponibiliteValidatorFactory
 * @package Application\Form\Disponibilite;
 */
class DisponibiliteValidatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DisponibiliteValidator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DisponibiliteValidator
    {
        $validator = new DisponibiliteValidator($options);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $validator->setObjectManager($entityManager);

        return $validator;
    }
}