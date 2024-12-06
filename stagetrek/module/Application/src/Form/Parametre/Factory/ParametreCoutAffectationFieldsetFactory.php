<?php


namespace Application\Form\Parametre\Factory;

use Application\Entity\Db\ParametreCoutAffectation;
use Application\Form\Parametre\Fieldset\ParametreCoutAffectationFieldset;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ParametreCoutAffectationFieldsetFactory
 * @package Application\Form\Factory\Fieldset
 */
class ParametreCoutAffectationFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreCoutAffectationFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreCoutAffectationFieldset
    {
        $fieldset = new ParametreCoutAffectationFieldset('parametreCoutAffectation');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new ParametreCoutAffectation());

        return $fieldset;
    }
}