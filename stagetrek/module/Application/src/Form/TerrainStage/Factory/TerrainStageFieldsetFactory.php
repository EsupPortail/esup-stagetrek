<?php


namespace Application\Form\TerrainStage\Factory;

use Application\Entity\Db\TerrainStage;
use Application\Form\Misc\Validator\CodeValidator;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Form\TerrainStage\Fieldset\TerrainStageFieldset;
use Application\Form\TerrainStage\Hydrator\TerrainStageHydrator;
use Application\Service\TerrainStage\TerrainStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class TerrainStageFieldsetFactory
 * @package Application\Form\TerrainStage\Factory
 */
class TerrainStageFieldsetFactory  implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Application\Form\TerrainStage\Fieldset\TerrainStageFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TerrainStageFieldset
    {
        $fieldset = new TerrainStageFieldset('terrainStage');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /**
         * @var TerrainStageService $terrainStageService
         */
        $terrainStageService = $container->get(ServiceManager::class)->get(TerrainStageService::class);

        /** @var TerrainStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(TerrainStageHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new TerrainStage());

        /** @var LibelleValidator $libelleValidator */
        $libelleValidator = $container->get(ValidatorPluginManager::class)->get(LibelleValidator::class);
        $libelleValidator->setEntityService($terrainStageService);
        $fieldset->setLibelleValidator($libelleValidator);
        /** @var CodeValidator $codeValidator */
        $codeValidator = $container->get(ValidatorPluginManager::class)->get(CodeValidator::class);
        $codeValidator->setEntityService($terrainStageService);
        $fieldset->setCodeValidator($codeValidator);

        $fieldset->setTagService($container->get(TagService::class));

        return $fieldset;
    }
}