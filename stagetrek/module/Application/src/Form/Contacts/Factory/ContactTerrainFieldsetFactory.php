<?php


namespace Application\Form\Contacts\Factory;

use Application\Entity\Db\ContactTerrain;
use Application\Form\Contacts\Fieldset\ContactTerrainFieldset;
use Application\Form\Contacts\Hydrator\ContactTerrainHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

class ContactTerrainFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContactTerrainFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContactTerrainFieldset
    {
        $fieldset = new ContactTerrainFieldset('contactTerrain');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $fieldset->setViewHelperManager($viewHelperManager);

        /** @var ContactTerrainHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ContactTerrainHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new ContactTerrain());

        return $fieldset;
    }
}