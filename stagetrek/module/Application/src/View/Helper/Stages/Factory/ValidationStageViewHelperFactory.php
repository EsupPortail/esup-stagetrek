<?php


namespace Application\View\Helper\Stages\Factory;

use Application\View\Helper\Stages\StageViewHelper;
use Application\View\Helper\Stages\ValidationStageViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UnicaenEtat\Service\EtatType\EtatTypeService;

/**
 * Class StageViewHelperFactory
 * @package Application\View\Helper\Stages
 */
class ValidationStageViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ValidationStageViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : ValidationStageViewHelper
    {
        $vh = new ValidationStageViewHelper();
        $vh->setEtatTypeService($container->get(EtatTypeService::class));
        return $vh;
    }

}