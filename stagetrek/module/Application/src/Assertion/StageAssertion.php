<?php


namespace Application\Assertion;

use Application\Controller\Stage\StageController as Controller;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\StagePrivileges;
use Application\Provider\Roles\RolesProvider;
use Application\Service\Etudiant\EtudiantService;
use Application\Validator\Actions\StageValidator;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\NonUniqueResultException;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class StageAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null): bool
   {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;

        $stage = $this->getStage();
        $session = $this->getSessionStage();
        return match ($action) {
            Controller::ACTION_LISTER,
            Controller::ACTION_MES_STAGES
                => $this->assertLister($session),
            Controller::ACTION_AFFICHER,
            Controller::ACTION_MON_STAGE,
            Controller::ACTION_AFFICHER_INFOS,
//                TODO : a spécifier plus en détail
            Controller::ACTION_AFFICHER_AFFECTATION,
            Controller::ACTION_AFFICHER_CONVENTION,
            Controller::ACTION_LISTER_CONTACTS
                => $this->assertAfficher($stage),
            Controller::ACTION_AJOUTER_STAGES
                => $this->assertAjouter($session),
            Controller::ACTION_SUPPRIMER_STAGES
                => $this->assertSupprimer($session),
            Controller::ACTION_MODIFIER_ORDRES_AFFECTATIONS
                => $this->assertModifierOrdres($session),
            default => false,
        };
    }

    protected function assertEntity(ResourceInterface $entity, $privilege = null): bool
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        // si le rôle ne possède pas le privilège
        if (!parent::assertEntity($entity, $privilege)) {
            return false;
        }

        $stage = ($entity instanceof Stage) ? $entity : null;
        $session = ($entity instanceof SessionStage) ? $entity : null;

        if ($entity instanceof ArrayRessource) {
            $stage = $entity->get(Stage::RESOURCE_ID);
            $session = $entity->get(SessionStage::RESOURCE_ID);
        }
        //Distinction pour l'affichage a partir d'une session => action lister
        if(isset($session) &&
            ($privilege == StagePrivileges::STAGE_AFFICHER
                || $privilege == StagePrivileges::ETUDIANT_OWN_STAGES_AFFICHER
            )
        ){
            return $this->assertLister($session);
        }

        return match ($privilege) {
            StagePrivileges::STAGE_AFFICHER,
            StagePrivileges::ETUDIANT_OWN_STAGES_AFFICHER =>
                $this->assertAfficher($stage),
            StagePrivileges::STAGE_AJOUTER => $this->assertAjouter($session),
            StagePrivileges::STAGE_MODIFIER => $this->assertModifierOrdres($session),
            StagePrivileges::STAGE_SUPPRIMER => $this->assertSupprimer($session),
            default => false,
        };
    }

    private function assertLister(?SessionStage $session) : bool
    {
        //Cas d'un étudiant : il peux toujours lister ces stages, sans préciser la session
        if($this->userIsStudent()){return true;}
        return isset($session);
    }

    private function assertAfficher(?Stage $stage) : bool
    {
        if(!isset($stage)){return false;}
        if(!$this->userIsStudent()){return true;}
        if(!$this->userIsStageOwner($stage)){return false;}

        $session = $stage->getSessionStage();
        if($session->hasEtatDesactive()){return false;}
        if($stage->hasEtatDesactive()){return false;}
        if($stage->hasEtatEnErreur()){return false;}

        return true;
    }

    private function assertAjouter(?SessionStage $session) : bool
    {
        return $this->getStageValidator()->assertAjouter($session);
    }

    private function assertSupprimer(?SessionStage $session) : bool
    {
        return $this->getStageValidator()->assertSupprimer($session);
    }

    private ?StageValidator $stageValidator = null;
    protected function getStageValidator() : StageValidator
    {
        if($this->stageValidator === null){
            $this->stageValidator = $this->getValidatorManager()->get(StageValidator::class);
        }
        return $this->stageValidator;
    }

    private function assertModifierOrdres(?SessionStage $session) : bool
    {
        return isset($session);
    }


    protected function userIsStudent() : bool
    {
        $role = $this->getRole();
        return $role->getRoleId() == RolesProvider::ETUDIANT;
    }

    protected function userIsStageOwner(Stage $stage) : bool
    {
        try {
            $etudiant = $this->getEtudiantFromUser();
        } catch (NotSupported | NonUniqueResultException | NotFoundExceptionInterface | ContainerExceptionInterface) {
            return false;
        }
        return $stage->getEtudiant()->getId() == $etudiant->getId();
    }


    protected function getStage() : ?Stage
    {
        $id = intval($this->getParam('stage', 0));
        return $this->getObjectManager()->getRepository(Stage::class)->find($id);
    }

    protected function getSessionStage() : ?SessionStage
    {
        $id = intval($this->getParam('sessionStage', 0));
        return $this->getObjectManager()->getRepository(SessionStage::class)->find($id);
    }

    /**
     * @return ?Etudiant
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getEtudiantFromUser() : ?Etudiant
    {
        $utilisateur = $this->getUser();
        /** @var EtudiantService $service */
        $service = $this->getServiceManager()->get(EtudiantService::class);
        return $service->findByAttribute($utilisateur, "user", true);
    }
}
