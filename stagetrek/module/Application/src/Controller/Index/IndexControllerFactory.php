<?php

namespace Application\Controller\Index;

use Application\Entity\Db\Adresse;
use Application\Entity\Db\AdresseType;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\MessageInfo;
use Application\Entity\Db\Parametre;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\Source;
use Application\Entity\Db\ValidationStage;
use Application\Service\Adresse\AdresseTypeService;
use Application\Service\Notification\MessageInfoService;
use Doctrine\ORM\EntityManager;
use http\Message;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenEtat\Entity\Db\EtatInstance;
use UnicaenEtat\Entity\Db\EtatType;
use UnicaenEvenement\Entity\Db\Evenement;
use UnicaenMail\Entity\Db\Mail;
use UnicaenPrivilege\Entity\Db\Privilege;
use UnicaenRenderer\Entity\Db\Rendu;
use UnicaenUtilisateur\Entity\Db\Role;
use UnicaenUtilisateur\Entity\Db\User;
use UnicaenUtilisateur\Provider\Privilege\UtilisateurPrivileges;

/**
 * Class IndexControllerFactory
 * @package Application\Controller\Factory
 */
class IndexControllerFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return IndexController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IndexController
    {
//        $config = $container->get('Configuration');
//        var_dump($config['controllers']['factories']);
//        var_dump($config["router"]['routes']['preferences']);
//        var_dump($config["router"]['routes']['etudiant']['child_routes']['afficher']);
//        var_dump($config["router"]['routes']['fichier']['child_routes']['upload']);
//        var_dump($config['unicaen-bddadmin']['data']);
//        die();

        $controller = new IndexController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        $messageInfoService = $container->get(ServiceManager::class)->get(MessageInfoService::class);
        $controller->setMessageInfoService($messageInfoService);
        return $controller;
    }

}