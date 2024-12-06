<?php

namespace Application\View\Renderer;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FlashMessageViewHelperFactory implements FactoryInterface
{
    /**
     * Create view helper
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return FlashMessageViewHelper|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $vh = new FlashMessageViewHelper();
//        $vh->setTranslator();

        return $vh;
    }
}