<?php


namespace Application\Assertion;

use Application\Controller\AnneeUniversitaire\AnneeUniversitaireController as Controller;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\AnneePrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class AnneeUniversitaireAssertion extends AbstractAssertion
{
    protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $annee = $this->getAnneeUniversitaire();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        //Cas spécifique
        if($action ==  Controller::ACTION_AFFICHER && !isset($annee)){return true;}
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AFFICHER, Controller::ACTION_AFFICHER_INFOS,
            Controller::ACTION_AFFICHER_CALENDRIER => $this->assertAfficher($annee),
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($annee),
            Controller::ACTION_SUPPRIMER =>  $this->assertSupprimer($annee),
            Controller::ACTION_VALIDER => $this->assertValider($annee),
            Controller::ACTION_DEVEROUILLER => $this->assertDeverouiller($annee),
            default => false,
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
        $annee = ($entity instanceof AnneeUniversitaire) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $annee = $entity->get(AnneeUniversitaire::RESOURCE_ID);
        }
        return match ($privilege) {
            AnneePrivileges::ANNEE_UNIVERSITAIRE_AFFICHER => $this->assertAfficher($annee),
            AnneePrivileges::ANNEE_UNIVERSITAIRE_AJOUTER => $this->assertAjouter(),
            AnneePrivileges::ANNEE_UNIVERSITAIRE_MODIFIER => $this->assertModifier($annee),
            AnneePrivileges::ANNEE_UNIVERSITAIRE_SUPPRIMER => $this->assertSupprimer($annee),
            AnneePrivileges::ANNEE_UNIVERSITAIRE_VALIDER => $this->assertValider($annee),
            AnneePrivileges::ANNEE_UNIVERSITAIRE_DEVERROUILLER => $this->assertDeverouiller($annee),
            default => false,
        };
    }


    private function assertAfficher(?AnneeUniversitaire $annee) : bool
    {
        return isset($annee);
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?AnneeUniversitaire $annee) : bool
    {
        if(!isset($annee)){return false;}
        if($annee->isLocked()){return false;}
        return true;
    }

    protected function assertSupprimer(?AnneeUniversitaire $annee): bool
    {
        if(!isset($annee)){return false;}
        if($annee->isLocked()){return false;}
        if(!$annee->getGroupes()->isEmpty()){return false;}
        if(!empty($annee->getSessionsStages())){return false;}
        return true;
    }
    protected function assertValider(?AnneeUniversitaire $annee): bool
    {
        if(!isset($annee)){return false;}
        if($annee->isLocked()){return false;}
        if($annee->getGroupes()->isEmpty()){return false;}
        /** @var \Application\Entity\Db\Groupe $g */
        foreach ($annee->getGroupes() as $g){
            if($g->getSessionsStages()->isEmpty()){return false;}
        }
        return true;
    }
    protected function assertDeverouiller(?AnneeUniversitaire $annee): bool
    {
        if(!isset($annee)){return false;}
        if(!$annee->isLocked()){return false;}
        return true;
    }

    protected function getAnneeUniversitaire() : ?AnneeUniversitaire
    {
        $id = intval($this->getParam('anneeUniversitaire', 0));
        return $this->getObjectManager()->getRepository(AnneeUniversitaire::class)->find($id);
    }

}