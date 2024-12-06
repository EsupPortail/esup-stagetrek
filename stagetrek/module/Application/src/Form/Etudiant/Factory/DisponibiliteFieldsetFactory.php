<?php


namespace Application\Form\Etudiant\Factory;

use Application\Entity\Db\Disponibilite;
use Application\Form\Etudiant\Fieldset\DisponibiliteFieldset;
use Application\Form\Etudiant\Hydrator\DisponibiliteHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class DisponibiliteFieldsetFactory
 * @package Application\Form\Disponibilite
 */
class DisponibiliteFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DisponibiliteFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DisponibiliteFieldset
    {
        $fieldset = new DisponibiliteFieldset('disponibilite');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var DisponibiliteHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DisponibiliteHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new Disponibilite());

        return $fieldset;
    }
}