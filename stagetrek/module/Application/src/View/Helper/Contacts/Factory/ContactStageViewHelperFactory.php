<?php


namespace Application\View\Helper\Contacts\Factory;

use Application\View\Helper\Contacts\ContactStageViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ContactStageViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContactStageViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var ContactStageViewHelper $vh
         */
        $vh = new ContactStageViewHelper();

        return $vh;
    }

}