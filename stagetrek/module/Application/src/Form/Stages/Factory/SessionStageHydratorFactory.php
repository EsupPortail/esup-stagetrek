<?php

namespace Application\Form\Stages\Factory;

use Application\Form\Stages\Hydrator\SessionStageHydrator;
use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Application\Service\Groupe\GroupeService;
use Application\Service\Parametre\ParametreService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenCalendrier\Service\CalendrierType\CalendrierTypeService;
use UnicaenCalendrier\Service\CalendrierType\CalendrierTypeServiceAwareTrait;
use UnicaenCalendrier\Service\DateType\DateTypeService;
use UnicaenCalendrier\Service\DateType\DateTypeServiceAwareTrait;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class SessionStageHydratorFactory
 * @package Application\Form\SessionsStages\Factory
 */
class SessionStageHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SessionStageHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SessionStageHydrator
    {
        $hydrator = new SessionStageHydrator();

        /** @var AnneeUniversitaireService $anneeUniversitaireService */
        $anneeUniversitaireService = $container->get(ServiceManager::class)->get(AnneeUniversitaireService::class);
        $hydrator->setAnneeUniversitaireService($anneeUniversitaireService);

        /** @var GroupeService $groupeService */
        $groupeService = $container->get(ServiceManager::class)->get(GroupeService::class);
        $hydrator->setGroupeService($groupeService);

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $hydrator->setParametreService($parametreService);

        $hydrator->setCalendrierTypeService($container->get(CalendrierTypeService::class));
        $hydrator->setDateTypeService($container->get(DateTypeService::class));

        $hydrator->setTagService($container->get(TagService::class));
        return $hydrator;
    }
}