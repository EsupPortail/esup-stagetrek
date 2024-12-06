<?php


namespace Application\View\Helper\ContrainteCursus\Factory;
use Application\View\Helper\ContrainteCursus\ContrainteCursusEtudiantViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ContrainteCursusEtudiantViewHelperFactory
 * @package Application\View\Helper\Etudiant\Factory
 */
class ContrainteCursusEtudiantViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContrainteCursusEtudiantViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusEtudiantViewHelper
    {
        return new ContrainteCursusEtudiantViewHelper();
    }

}