<?php

namespace Application\Form\Affectation\Factory;

use Application\Entity\Db\AffectationStage;
use Application\Form\Affectation\Fieldset\ProcedureAffectationFieldset;
use Application\Form\Misc\Validator\CodeValidator;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Service\Affectation\ProcedureAffectationService;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class AffectationStageFieldsetFactory
 * @package Application\Form\AffectationStage\Factory
 */
class ProcedureAffectationFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ProcedureAffectationFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ProcedureAffectationFieldset
    {
        $fieldset = new ProcedureAffectationFieldset('procedureAffectation');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /**
         * @var ProcedureAffectationService $contactService
         */
        $contactService = $container->get(ServiceManager::class)->get(ProcedureAffectationService::class);
        /** @var CodeValidator $codeValidator */
        $codeValidator = $container->get(ValidatorPluginManager::class)->get(CodeValidator::class);
        $codeValidator->setEntityService($contactService);
        $fieldset->setCodeValidator($codeValidator);

        /** @var LibelleValidator $libelleValidator */
        $libelleValidator = $container->get(ValidatorPluginManager::class)->get(LibelleValidator::class);
        $libelleValidator->setEntityService($contactService);
        $fieldset->setLibelleValidator($libelleValidator);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $fieldset->setHydrator($hydrator);

        $fieldset->setObject(new AffectationStage());

        return $fieldset;
    }
}