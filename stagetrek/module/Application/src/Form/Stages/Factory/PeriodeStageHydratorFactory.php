<?php

namespace Application\Form\Stages\Factory;

use Application\Form\Stages\Hydrator\PeriodeStageHydrator;
use Application\Form\Stages\Hydrator\SessionStageHydrator;
use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Application\Service\Groupe\GroupeService;
use Application\Service\Parametre\ParametreService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenCalendrier\Service\CalendrierType\CalendrierTypeService;
use UnicaenCalendrier\Service\DateType\DateTypeService;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class SessionStageHydratorFactory
 * @package Application\Form\SessionsStages\Factory
 */
class PeriodeStageHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PeriodeStageHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PeriodeStageHydrator
    {
        $hydrator = new PeriodeStageHydrator();
        return $hydrator;
    }
}