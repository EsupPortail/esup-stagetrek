<?php


namespace Application\Form\Referentiel\Factory;

use Application\Entity\Db\ReferentielPromo;
use Application\Form\Misc\Validator\CodeValidator;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Form\Referentiel\Fieldset\ReferentielPromoFieldset;
use Application\Form\Referentiel\Hydrator\ReferentielPromoHydrator;
use Application\Service\Referentiel\ReferentielPromoService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class ReferentielPromoFieldsetFactory
 * @package Application\Form\Factory\Fieldset
 */
class ReferentielPromoFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ReferentielPromoFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ReferentielPromoFieldset
    {
        $fieldset = new ReferentielPromoFieldset('referentielPromo');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var ReferentielPromoHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ReferentielPromoHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new ReferentielPromo());

        /** @var CodeValidator $codeValidator */
        $codeValidator = $container->get(ValidatorPluginManager::class)->get(CodeValidator::class);
        $codeValidator->setEntityService($container->get(ReferentielPromoService::class));
        $fieldset->setCodeValidator($codeValidator);

        /** @var LibelleValidator $libelleValidator */
        $libelleValidator = $container->get(ValidatorPluginManager::class)->get(LibelleValidator::class);
        $libelleValidator->setEntityService($container->get(ReferentielPromoService::class));
        $fieldset->setLibelleValidator($libelleValidator);

        return $fieldset;
    }
}