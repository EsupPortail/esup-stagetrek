<?php

namespace Application\Form\Affectation\Factory;

use Application\Entity\Db\AffectationStage;
use Application\Form\Affectation\Fieldset\AffectationStageFieldset;
use Application\Form\Affectation\Hydrator\AffectationStageHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class AffectationStageFieldsetFactory
 * @package Application\Form\AffectationStage\Factory
 */
class AffectationStageFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AffectationStageFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AffectationStageFieldset
    {
        $fieldset = new AffectationStageFieldset('affectationStage');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var AffectationStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(AffectationStageHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new AffectationStage());

        $fieldset->setTagService($container->get(TagService::class));

        return $fieldset;
    }
}