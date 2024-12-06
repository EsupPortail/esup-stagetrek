<?php


namespace Application\View\Helper\Groupe\Factory;

use Application\View\Helper\Groupe\GroupeViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class GroupeViewHelperFactory
 * @package Application\View\Helper\Groupe
 */
class GroupeViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return GroupeViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GroupeViewHelper
    {
        /**
         * @var GroupeViewHelper $vh
         */
        $vh = new GroupeViewHelper();

        return $vh;
    }

}