<?php


namespace Application\Form\Notification\Factory;

use Application\Entity\Db\MessageInfo;
use Application\Form\Notification\Fieldset\MessageInfoFieldset;
use Application\Form\Notification\Hydrator\MessageInfoHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class MessageInfoFieldsetFactory
 * @package Application\Form\Message\Factory;
 */
class MessageInfoFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return MessageInfoFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MessageInfoFieldset
    {
        $fieldset = new MessageInfoFieldset('messageInfo');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $fieldset->setViewHelperManager($viewHelperManager);

        /** @var MessageInfoHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(MessageInfoHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new MessageInfo());

        return $fieldset;
    }
}