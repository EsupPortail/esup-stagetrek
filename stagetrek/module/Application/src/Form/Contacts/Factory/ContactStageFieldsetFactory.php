<?php


namespace Application\Form\Contacts\Factory;

use Application\Entity\Db\ContactStage;
use Application\Form\Contacts\Fieldset\ContactStageFieldset;
use Application\Form\Contacts\Hydrator\ContactStageHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ContactStageFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Application\Form\Contacts\Fieldset\ContactStageFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : ContactStageFieldset
    {
        $fieldset = new ContactStageFieldset('contactStage');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var ContactStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ContactStageHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new ContactStage());

        return $fieldset;
    }
}