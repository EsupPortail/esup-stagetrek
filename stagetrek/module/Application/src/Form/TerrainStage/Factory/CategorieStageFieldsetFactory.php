<?php


namespace Application\Form\TerrainStage\Factory;

use Application\Entity\Db\CategorieStage;
use Application\Form\Misc\Validator\AcronymeValidator;
use Application\Form\Misc\Validator\CodeValidator;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Form\TerrainStage\Fieldset\CategorieStageFieldset;
use Application\Service\TerrainStage\CategorieStageService;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class CategorieStageFieldset
 * @package Application\Form\TerrainStage\Factory
 */
class CategorieStageFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CategorieStageFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CategorieStageFieldset
    {
        $fieldset = new CategorieStageFieldset('categorieStage');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /**
         * @var CategorieStageService $categorieStageService
         */
        $categorieStageService = $container->get(ServiceManager::class)->get(CategorieStageService::class);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new CategorieStage());

        /** @var CodeValidator $codeValidator */
        $codeValidator = $container->get(ValidatorPluginManager::class)->get(CodeValidator::class);
        $codeValidator->setEntityService($categorieStageService);
        $fieldset->setCodeValidator($codeValidator);

        /** @var LibelleValidator $libelleValidator */
        $libelleValidator = $container->get(ValidatorPluginManager::class)->get(LibelleValidator::class);
        $libelleValidator->setEntityService($categorieStageService);
        $fieldset->setLibelleValidator($libelleValidator);


        /** @var AcronymeValidator $acronymeValidator */
        $acronymeValidator = $container->get(ValidatorPluginManager::class)->get(AcronymeValidator::class);
        $acronymeValidator->setEntityService($categorieStageService);
        $fieldset->setAcronymeValidator($acronymeValidator);

        return $fieldset;
    }
}