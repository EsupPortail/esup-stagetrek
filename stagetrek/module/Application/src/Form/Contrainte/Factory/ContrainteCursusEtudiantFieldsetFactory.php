<?php


namespace Application\Form\Contrainte\Factory;

use Application\Form\Contrainte\Fieldset\ContrainteCursusEtudiantFieldset;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ContrainteCursusEtudiantFieldsetFactory
 * @package Application\Form\ContraintesCursus\Factory
 */
class ContrainteCursusEtudiantFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContrainteCursusEtudiantFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusEtudiantFieldset
    {
        $fieldset = new ContrainteCursusEtudiantFieldset('contrainteCursusEtudiant');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new ContrainteCursusEtudiantFieldset());

        return $fieldset;
    }
}