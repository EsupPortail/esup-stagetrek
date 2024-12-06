<?php


namespace Application\Form\Stages\Factory;

use Application\Entity\Db\ValidationStage;
use Application\Form\Stages\Fieldset\ValidationStageFieldset;
use Application\Form\Stages\Hydrator\ValidationStageHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ValidationStageFieldsetFactory
 * @package Application\Form\Stages\Factory
 */
class ValidationStageFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ValidationStageFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ValidationStageFieldset
    {
        $fieldset = new ValidationStageFieldset('validationStage');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var ValidationStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ValidationStageHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new ValidationStage());

        return $fieldset;
    }
}