<?php


namespace Application\Form\Etudiant\Factory;

use Application\Entity\Db\Etudiant;
use Application\Form\Etudiant\Fieldset\EtudiantFieldset;
use Application\Form\Etudiant\Hydrator\EtudiantHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class EtudiantFieldsetFactory
 * @package Application\Form\Etudiant\Factory
 */
class EtudiantFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EtudiantFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EtudiantFieldset
    {
        $fieldset = new EtudiantFieldset('etudiant');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var EtudiantHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(EtudiantHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new Etudiant());

        return $fieldset;
    }
}