<?php


namespace Application\Form\Groupe\Factory;

use Application\Entity\Db\Groupe;
use Application\Form\Groupe\Fieldset\GroupeFieldset;
use Application\Form\Groupe\Hydrator\GroupeHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class GroupeFieldsetFactory
 * @package Application\Form\Groupe\Factory;
 */
class GroupeFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return GroupeFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GroupeFieldset
    {
        $fieldset = new GroupeFieldset('groupe');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var GroupeHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(GroupeHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new Groupe());

        return $fieldset;
    }
}