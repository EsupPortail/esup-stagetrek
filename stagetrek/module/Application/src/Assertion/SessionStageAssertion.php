<?php


namespace Application\Assertion;

use Application\Controller\Stage\SessionStageController as Controller;
use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\SessionStage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ContactPrivileges;
use Application\Provider\Privilege\SessionPrivileges;
use Application\Provider\Privilege\StagePrivileges;
use DateTime;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use UnicaenCalendrier\Entity\Db\Date;

class SessionStageAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $session = $this->getSessionStage();
        $annee = $this->getAnneeUniversitaire();
        $periode = $this->getDate();

        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX => true,
            Controller::ACTION_AFFICHER, Controller::ACTION_AFFICHER_INFOS, Controller::ACTION_AFFICHER_DATES
                => $this->assertAfficher($session),
            Controller::ACTION_AJOUTER => $this->assertAjouter($annee),
            Controller::ACTION_MODIFIER,  Controller::ACTION_MODIFIER_PLACES_TERRAINS
                => $this->assertModifier($session),
            Controller::ACTION_MODIFIER_ORDRES_AFFECTATIONS
                => $this->assertModifierOrdreAffectation($session),
            Controller::ACTION_RECALCULER_ORDRES_AFFECTATIONS
                => $this->assertRecacluerOrdreAffectation($session),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($session),
            Controller::ACTION_IMPORTER_PLACES_TERRAINS => $this->assertImporter($session),
            Controller::ACTION_AJOUTER_PERIODE_STAGE => $this->assertAjouterPeriode($session),
            Controller::ACTION_MODIFIER_PERIODE_STAGE => $this->assertModifierPeriode($session, $periode),
            Controller::ACTION_SUPPRIMER_PERIODE_STAGE => $this->assertSupprimerPeriode($session,$periode),
            default =>false,
        };
    }


    protected function assertEntity(ResourceInterface $entity, $privilege = null)
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        // si le rôle ne possède pas le privilège
        if (!parent::assertEntity($entity, $privilege)) {
            return false;
        }

        $session = ($entity instanceof SessionStage) ? $entity : null;
        $annee = ($entity instanceof AnneeUniversitaire) ? $entity : null;
        $periode = null;
        $action = null;
        if ($entity instanceof ArrayRessource) {
            $session = $entity->get(SessionStage::RESOURCE_ID);
            $annee = $entity->get(AnneeUniversitaire::RESOURCE_ID);
            $periode = $entity->get(Date::class);
            $action = $entity->get('action');
        }
        if($privilege == SessionPrivileges::SESSION_STAGE_MODIFIER) {
            if ($action == Controller::ACTION_RECALCULER_ORDRES_AFFECTATIONS) {
                return $this->assertRecacluerOrdreAffectation($session);
            }
            if ($action == Controller::ACTION_AJOUTER_PERIODE_STAGE) {
                return $this->assertAjouterPeriode($session);
            }
            if ($action == Controller::ACTION_MODIFIER_PERIODE_STAGE) {
                return $this->assertModifierPeriode($session, $periode);
            }
            elseif ($action == Controller::ACTION_SUPPRIMER_PERIODE_STAGE) {
                return $this->assertSupprimerPeriode($session, $periode);
            }
        }

        return match ($privilege) {
            SessionPrivileges::SESSION_STAGE_AFFICHER => $this->assertAfficher($session),
            SessionPrivileges::SESSION_STAGE_AJOUTER => $this->assertAjouter($annee),
            SessionPrivileges::SESSION_STAGE_MODIFIER => $this->assertModifier($session),
            StagePrivileges::STAGE_MODIFIER => $this->assertModifierOrdreAffectation($session),
            SessionPrivileges::SESSION_STAGE_SUPPRIMER => $this->assertSupprimer($session),
//            TODO : revoir pour les terrains de stages
            default => false,
        };
    }


    private function assertAfficher(?SessionStage $sessionStage) : bool
    {
        return isset($sessionStage);
    }

    private function assertAjouter(?AnneeUniversitaire $annee) : bool
    {   //L'ajout d'une session de stages doit nécessairement se faire sur une année existante
        if(!isset($annee)){ return false;}
        if($annee->isLocked()){return false;}
        if($annee->getGroupes()->isEmpty()){return false;}
        return true;
    }

    private function assertModifier(?SessionStage $sessionStage) : bool
    {
        if(!isset($sessionStage)){return false;}
        return true;
    }

    private function assertModifierOrdreAffectation(?SessionStage $sessionStage) : bool
    {
        if(!isset($sessionStage)){return false;}
        if(empty($sessionStage->getStagesPrincipaux())){return false;}
        return true;
    }
    private function assertRecacluerOrdreAffectation(?SessionStage $sessionStage) : bool
    {
        if(!isset($sessionStage)){return false;}
        if(empty($sessionStage->getStagesPrincipaux())){return false;}
        return true;
    }

    private function assertSupprimer(?SessionStage $sessionStage) : bool
    {
        if(!isset($sessionStage)){return false;}
        $annee = $sessionStage->getAnneeUniversitaire();
        if($annee->isLocked()){return false;}
        /** @var AffectationStage $affectation */
        foreach ($sessionStage->getAffectations() as $affectation) {
            if ($affectation->hasEtatValidee()) {
                return false;
            }
        }
        return true;
    }

    protected function getAnneeUniversitaire() : ?AnneeUniversitaire
    {
        $id = intval($this->getParam('anneeUniversitaire'));
        return $this->getObjectManager()->getRepository(AnneeUniversitaire::class)->find($id);
    }

    protected function getSessionStage() : ?SessionStage
    {
        $id = intval($this->getParam('sessionStage'));
        return $this->getObjectManager()->getRepository(SessionStage::class)->find($id);
    }
    protected function getDate() : ?Date
    {
        $id = intval($this->getParam('date'));
        return $this->getObjectManager()->getRepository(Date::class)->find($id);
    }


    private function assertImporter(?SessionStage $session) : bool
    {
        if(!isset($session)){return false;}
        return false; // Pages d'import a revoir
    }

    private function assertAjouterPeriode(?SessionStage $session) : bool
    {
        if(!isset($session)){return false;}
        if(!$session->hasMultiplesPeriodesStage()){return false;}
        return true;
    }
    private function assertModifierPeriode(?SessionStage $session, ?Date $periode) : bool
    {
        return true;
        if(!isset($session)){return false;}
        if(!isset($periode)){return false;}
        if(!$session->hasMultiplesPeriodesStage()){return false;}
        if(!$periode->getType()->getCode() != SessionStage::DATES_PERIODE_STAGES){return false;}
        $periodes = $session->getDatesPeriodesStages();
        if(!isset($periodes) || empty($periodes)){return false;}
        foreach ($periodes as $periode2) {
            if($periode2->getId() == $periode->getId()){
                return true;
            }
        }
        return false;
    }

    private function assertSupprimerPeriode(?SessionStage $session, ?Date $periode) : bool
    {
        return true;
        if(!isset($session)){return false;}
        if(!isset($periode)){return false;}
        if(!$session->hasMultiplesPeriodesStage()){return false;}
        if(!$periode->getType()->getCode() != SessionStage::DATES_PERIODE_STAGES){return false;}
        $periodes = $session->getDatesPeriodesStages();
        if(!isset($periodes) || empty($periodes)){return false;}
        foreach ($periodes as $periode2) {
            if($periode2->getId() == $periode->getId()){
                return true;
            }
        }
        return false;
    }
}
