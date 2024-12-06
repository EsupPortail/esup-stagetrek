<?php


namespace Application\Form\Parametre\Factory;

use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Form\Parametre\Fieldset\ParametreCoutTerrainFieldset;
use Application\Form\Parametre\Hydrator\ParametreCoutTerrainHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ParametreTerrainCoutAffectationFixeFieldsetFactory
 * @package Application\Form\Factory\Fieldset
 */
class ParametreCoutTerrainFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreCoutTerrainFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreCoutTerrainFieldset
    {
        $fieldset = new ParametreCoutTerrainFieldset('parametreTerrainCoutAffectationFixe');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var ParametreCoutTerrainHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ParametreCoutTerrainHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new ParametreTerrainCoutAffectationFixe());

        return $fieldset;
    }
}