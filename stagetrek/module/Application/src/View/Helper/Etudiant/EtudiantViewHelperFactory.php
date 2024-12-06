<?php


namespace Application\View\Helper\Etudiant;

use Application\Service\Etudiant\EtudiantService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class EtudiantViewHelperFactory
 * @package Application\View\Helper\Etudiant
 */
class EtudiantViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EtudiantViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var EtudiantViewHelper $vh
         */
        $vh = new EtudiantViewHelper();

        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $vh->setEtudiantService($etudiantService);

        return $vh;
    }

}