<?php

namespace Application\Form\Notification\Factory;

use Application\Form\Notification\Hydrator\MessageInfoHydrator;
use Application\Form\Notification\MessageInfoForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class MessageInfoFormFactory
 * @package Application\Form\Message\Factory;
 */
class MessageInfoFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return MessageInfoForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MessageInfoForm
    {
        $form = new MessageInfoForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var MessageInfoHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(MessageInfoHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}
