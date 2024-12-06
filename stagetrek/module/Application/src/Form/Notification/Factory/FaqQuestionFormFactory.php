<?php

namespace Application\Form\Notification\Factory;

use Application\Form\Notification\FaqQuestionForm;
use Application\Form\Notification\Hydrator\FaqQuestionHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class FaqQuestionFormFactory
 */
class FaqQuestionFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return FaqQuestionForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FaqQuestionForm
    {
        $form = new FaqQuestionForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var FaqQuestionHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(FaqQuestionHydrator::class);
        $form->setHydrator($hydrator);
        return $form;
    }

}
