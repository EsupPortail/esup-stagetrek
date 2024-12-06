<?php


namespace Application\View\Helper\Notification\Factory;

use Application\View\Helper\Notification\MessageInfoViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class MessageInfoViewHelperFactory
 * @package Application\View\Helper\Messages\Factory
 */
class MessageInfoViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return MessageInfoViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MessageInfoViewHelper
    {
        return new MessageInfoViewHelper();
    }

}