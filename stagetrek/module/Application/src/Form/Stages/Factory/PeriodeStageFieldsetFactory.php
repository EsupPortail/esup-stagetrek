<?php


namespace Application\Form\Stages\Factory;

use Application\Entity\Db\SessionStage;
use Application\Form\Stages\Fieldset\PeriodeStageFieldset;
use Application\Form\Stages\Fieldset\SessionStageFieldset;
use Application\Form\Stages\Hydrator\PeriodeStageHydrator;
use Application\Form\Stages\Hydrator\SessionStageHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class SessionStageFieldsetFactory
 * @package Applicat\Form\SessionsStages\Factory
 */
class PeriodeStageFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PeriodeStageFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PeriodeStageFieldset
    {
        $fieldset = new PeriodeStageFieldset('periodeStage');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var PeriodeStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(PeriodeStageHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new SessionStage());

        return $fieldset;
    }
}