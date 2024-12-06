<?php


namespace Application\View\Helper\Preferences\Factory;

use Application\Service\Contrainte\ContrainteCursusEtudiantService;
use Application\Service\Parametre\ParametreService;
use Application\View\Helper\Preferences\PreferenceViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class PreferenceViewHelperFactory
 * @package Application\View\Helper\Etudiant\Factory
 */
class PreferenceViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PreferenceViewHelper
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PreferenceViewHelper
    {
        $vh = new PreferenceViewHelper();

        /** @var ContrainteCursusEtudiantService $contrainteCursusEtudiantServics */
        $contrainteCursusEtudiantService = $container->get(ServiceManager::class)->get(ContrainteCursusEtudiantService::class);
        $vh->setContrainteCursusEtudiantService($contrainteCursusEtudiantService);

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $vh->setParametreService($parametreService);

        return $vh;
    }

}