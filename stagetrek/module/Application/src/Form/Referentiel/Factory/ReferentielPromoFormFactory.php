<?php

namespace Application\Form\Referentiel\Factory;

use Application\Form\Referentiel\Hydrator\ReferentielPromoHydrator;
use Application\Form\Referentiel\ReferentielPromoForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class ReferentielPromoFormFactory
 * @package Application\Form\Factory
 */
class ReferentielPromoFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ReferentielPromoForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ReferentielPromoForm
    {
        $form = new ReferentielPromoForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var ReferentielPromoHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ReferentielPromoHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}
