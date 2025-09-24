<?php


namespace Application\Form\Referentiel\Factory;

use Application\Form\Referentiel\Interfaces\AbstractImportEtudiantsForm;
use Application\Form\Referentiel\Interfaces\AbstractImportEtudiantsHydrator;
use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Application\Service\Referentiel\ReferentielPromoService;
use Interop\Container\Containerinterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class DisponibiliteHydratorFactory
 * @package Application\Form\Disponibilite
 */
class ImportEtudiantsHydratorFactory implements FactoryInterface
{
    /**
     * Can the factory create an instance for the service
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return class_exists($requestedName)
            && in_array(AbstractImportEtudiantsHydrator::class, class_implements($requestedName));
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AbstractImportEtudiantsHydrator
    {
        $hydrator = new $requestedName;
        if(method_exists($hydrator, 'setReferentielPromoService')){
            $hydrator->setReferentielPromoService($container->get(ReferentielPromoService::class));
        }
        if(method_exists($hydrator, 'setAnneeUniversitaireService')){
            $hydrator->setAnneeUniversitaireService($container->get(AnneeUniversitaireService::class));
        }
        return $hydrator;
    }
}