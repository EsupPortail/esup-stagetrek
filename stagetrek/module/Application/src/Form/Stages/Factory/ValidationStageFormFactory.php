<?php

namespace Application\Form\Stages\Factory;

use Application\Entity\Db\ValidationStage;
use Application\Form\Stages\Hydrator\ValidationStageHydrator;
use Application\Form\Stages\ValidationStageForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class ValidationStageFormFactory
 * @package Application\Form\Stages\Factory
 */
class ValidationStageFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ValidationStageForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ValidationStageForm
    {
        $form = new ValidationStageForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var ValidationStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ValidationStageHydrator::class);
        $form->setHydrator($hydrator);
        $form->setObject(new ValidationStage());

        return $form;
    }
}
