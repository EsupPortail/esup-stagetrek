<?php

namespace Application\Form\Convention\Factory;

use Application\Entity\Db\ModeleConventionStage;
use Application\Form\Convention\Fieldset\ModeleConventionStageFieldset;
use Application\Form\Convention\Hydrator\ModeleConventionStageHydrator;
use Application\Form\Misc\Validator\CodeValidator;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Service\ConventionStage\ModeleConventionStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

class ModeleConventionStageFieldsetFactory  implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ModeleConventionStageFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ModeleConventionStageFieldset
    {
        $fieldset = new ModeleConventionStageFieldset('modeleConventionStage');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);


        /**
         * @var ModeleConventionStageService $modeleConventionStageService
         */
        $modeleConventionStageService = $container->get(ServiceManager::class)->get(ModeleConventionStageService::class);


        /** @var ModeleConventionStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ModeleConventionStageHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new ModeleConventionStage());

        /** @var LibelleValidator $libelleValidator */
        $libelleValidator = $container->get(ValidatorPluginManager::class)->get(LibelleValidator::class);
        $libelleValidator->setEntityService($modeleConventionStageService);
        $fieldset->setLibelleValidator($libelleValidator);
        /** @var CodeValidator $codeValidator */
        $codeValidator = $container->get(ValidatorPluginManager::class)->get(CodeValidator::class);
        $codeValidator->setEntityService($modeleConventionStageService);
        $fieldset->setCodeValidator($codeValidator);

        return $fieldset;
    }
}