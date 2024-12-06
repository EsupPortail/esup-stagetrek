<?php

namespace Application\Form\Convention\Factory;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\ConventionStage;
use Application\Form\Convention\Fieldset\ConventionStageTeleversementFieldset;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ConventionStageTeleversementFieldsetFactory  implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ConventionStageTeleversementFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConventionStageTeleversementFieldset
    {
        $fieldset = new ConventionStageTeleversementFieldset('conventionStage');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $fieldset->setHydrator($hydrator);

        $fieldset->setObject(new ConventionStage());

        $config = $container->get('Config');
        if(isset($config['fichier']['uplpoad'])){
            $fieldset->setFileConfig($config['fichier']['uplpoad']);
        }

        return $fieldset;
    }
}