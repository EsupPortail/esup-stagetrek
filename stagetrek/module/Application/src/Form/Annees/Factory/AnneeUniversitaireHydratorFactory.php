<?php

namespace Application\Form\Annees\Factory;

use Application\Form\Annees\Hydrator\AnneeUniversitaireHydrator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class EtudiantHydratorFactory
 * @package Application\Form\Etudiant\Factory
 */
class AnneeUniversitaireHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AnneeUniversitaireHydrator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnneeUniversitaireHydrator
    {
        $hydrator = new AnneeUniversitaireHydrator();
        $hydrator->setTagService($container->get(TagService::class));
        return $hydrator;
    }

}