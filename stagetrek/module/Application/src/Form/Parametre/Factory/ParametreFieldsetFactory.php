<?php


namespace Application\Form\Parametre\Factory;

use Application\Entity\Db\Parametre;
use Application\Form\Parametre\Fieldset\ParametreFieldset;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ParametreFieldsetFactory
 * @package Application\Form\Factory\Fieldset
 */
class ParametreFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreFieldset
    {
        $fieldset = new ParametreFieldset('parametre');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new Parametre());

        return $fieldset;
    }
}