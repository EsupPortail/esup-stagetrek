<?php

namespace Application\Controller\Groupe\Factory;

use Application\Controller\Groupe\GroupeController;
use Application\Form\Groupe\GroupeForm;
use Application\Form\Groupe\GroupeRechercheForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Groupe\GroupeService;
use Application\Validator\Actions\GroupeValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class GroupeControllerFactory
 * @package Application\Controller\Factory
 */
class GroupeControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return GroupeController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GroupeController
    {
        $controller = new GroupeController();


        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var GroupeService $groupeService */
        $groupeService = $container->get(ServiceManager::class)->get(GroupeService::class);
        $controller->setGroupeService($groupeService);

        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $controller->setEtudiantService($etudiantService);

        /** @var AnneeUniversitaireService $anneeService */
        $anneeService = $container->get(ServiceManager::class)->get(AnneeUniversitaireService::class);
        $controller->setAnneeUniversitaireService($anneeService);


        /** @var GroupeForm $groupeForm */
        $groupeForm = $container->get(FormElementManager::class)->get(GroupeForm::class);
        $controller->setGroupeForm($groupeForm);

        /** @var GroupeRechercheForm $groupeRechercheForm */
        $groupeRechercheForm = $container->get(FormElementManager::class)->get(GroupeRechercheForm::class);
        $controller->setGroupeRechercheForm($groupeRechercheForm);
//
        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);


        /** @var \Application\Validator\Actions\GroupeValidator $validator */
        $validator = $container->get(ValidatorPluginManager::class)->get(GroupeValidator::class);
        $controller->setGroupeValidator($validator);


        return $controller;
    }
}