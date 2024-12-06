<?php


namespace Application\Controller\Preference\Factory;

use Application\Controller\Preference\PreferenceController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Preferences\PreferenceForm;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Parametre\ParametreService;
use Application\Service\Preference\PreferenceService;
use Application\Service\Stage\StageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class PreferencesControllerFactory
 * @package Application\Controller\Etudiants\Factory
 */
class PreferenceControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PreferenceController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PreferenceController
    {
        $controller = new PreferenceController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $controller->setEtudiantService($etudiantService);

        /** @var PreferenceService $preferenceService */
        $preferenceService = $container->get(ServiceManager::class)->get(PreferenceService::class);
        $controller->setPreferenceService($preferenceService);

        /** @var StageService $stageService */
        $stageService = $container->get(ServiceManager::class)->get(StageService::class);
        $controller->setStageService($stageService);

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $controller->setParametreService($parametreService);

        /** @var PreferenceForm $preferenceForm */
        $preferenceForm = $container->get(FormElementManager::class)->get(PreferenceForm::class);
        $controller->setPreferenceForm($preferenceForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);


        return $controller;
    }

}