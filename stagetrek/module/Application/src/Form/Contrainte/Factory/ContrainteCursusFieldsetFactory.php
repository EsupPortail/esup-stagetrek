<?php


namespace Application\Form\Contrainte\Factory;

use Application\Entity\Db\ContrainteCursus;
use Application\Form\Contrainte\Fieldset\ContrainteCursusFieldset;
use Application\Form\Contrainte\Hydrator\ContrainteCursusHydrator;
use Application\Form\Misc\Validator\AcronymeValidator;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Service\Contrainte\ContrainteCursusService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class ContrainteCursusFieldsetFactory
 * @package Application\Form\ContraintesCursus\Factory
 */
class ContrainteCursusFieldsetFactory  implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContrainteCursusFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusFieldset
    {
        $fieldset = new ContrainteCursusFieldset('contrainteCursus');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var ContrainteCursusHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ContrainteCursusHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new ContrainteCursus());

        /**
         * @var ContrainteCursusService $contrainteCursusService
         */
        $contrainteCursusService = $container->get(ServiceManager::class)->get(ContrainteCursusService::class);

        /** @var AcronymeValidator $acronymeValidator */
        $acronymeValidator = $container->get(ValidatorPluginManager::class)->get(AcronymeValidator::class);
        $acronymeValidator->setEntityService($contrainteCursusService);
        $fieldset->setAcronymeValidator($acronymeValidator);

        /** @var LibelleValidator $libelleValidator */
        $libelleValidator = $container->get(ValidatorPluginManager::class)->get(LibelleValidator::class);
        $libelleValidator->setEntityService($contrainteCursusService);
        $fieldset->setLibelleValidator($libelleValidator);

        return $fieldset;
    }
}