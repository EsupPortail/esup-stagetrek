<?php


namespace Application\Form\Stages\Factory;

use Application\Entity\Db\SessionStage;
use Application\Form\Stages\Fieldset\SessionStageFieldset;
use Application\Form\Stages\Hydrator\SessionStageHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class SessionStageFieldsetFactory
 * @package Applicat\Form\SessionsStages\Factory
 */
class SessionStageFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SessionStageFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SessionStageFieldset
    {
        $fieldset = new SessionStageFieldset('sessionStage');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var SessionStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(SessionStageHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new SessionStage());

        $fieldset->setTagService($container->get(TagService::class));

        return $fieldset;
    }
}