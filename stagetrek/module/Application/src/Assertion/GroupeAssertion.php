<?php


namespace Application\Assertion;

use Application\Controller\Groupe\GroupeController;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\Stage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\EtudiantPrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class GroupeAssertion extends AbstractAssertion
{
    /**
     * @param string $controller
     * @param string|null $action
     * @param string|null $privilege
     * @return boolean
     */
   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();
        if (!$role instanceof RoleInterface) return false;

        $groupe = $this->getGroupe();
        $annee = $this->getAnneeUniversitaire();
        return match ($action) {
            GroupeController::ACTION_INDEX => true,
            GroupeController::ACTION_AFFICHER, GroupeController::ACTION_AFFICHER_INFOS, GroupeController::ACTION_LISTER_SESSIONS, GroupeController::ACTION_LISTER_ETUDIANTS
                => $this->assertAfficher($groupe),
            GroupeController::ACTION_AJOUTER => $this->assertAjouter($annee),
            GroupeController::ACTION_MODIFIER => $this->assertModifier($groupe),
            GroupeController::ACTION_AJOUTER_ETUDIANTS, GroupeController::ACTION_RETIRER_ETUDIANTS
                => $this->assertAdministrerEtudiant($groupe),
            GroupeController::ACTION_SUPPRIMER
                => $this->assertSupprimer($groupe),
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

        $groupe = ($entity instanceof Groupe) ? $entity : null;
        $annee = ($entity instanceof AnneeUniversitaire) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $groupe = $entity->get(Groupe::RESOURCE_ID);
            $annee = $entity->get(AnneeUniversitaire::RESOURCE_ID);
        }
        return match ($privilege) {
            EtudiantPrivileges::GROUPE_AFFICHER => $this->assertAfficher($groupe),
            EtudiantPrivileges::GROUPE_ADMINISTRER_ETUDIANTS => $this->assertAdministrerEtudiant($groupe),
            EtudiantPrivileges::GROUPE_AJOUTER => $this->assertAjouter($annee),
            EtudiantPrivileges::GROUPE_MODIFIER => $this->assertModifier($groupe),
            EtudiantPrivileges::GROUPE_SUPPRIMER => $this->assertSupprimer($groupe),
            default => false,
        };
    }


    protected function assertAfficher(?Groupe $groupe): bool
    {
        if(!isset($groupe)){return false;}
        return true;
    }

    protected function assertAjouter(?AnneeUniversitaire $annee): bool
    {
        if(!isset($annee)){return false;}
        if($annee->isAnneeVerrouillee()){return false;}
        return true;
    }

    protected function assertModifier(?Groupe $groupe): bool
    {
        if(!isset($groupe)){return false;}
        return true;
    }

    protected function assertAdministrerEtudiant(?Groupe $groupe): bool
    {
        if(!isset($groupe)){return false;}
        return true;
    }

    protected function assertSupprimer(?Groupe $groupe): bool
    {
        if(!isset($groupe)){return false;}
        $annee = $groupe->getAnneeUniversitaire();
        if(!isset($annee)){return false;}
        if($annee->isAnneeVerrouillee()){return false;}
        /** @var Etudiant $etudiant */
        foreach ($groupe->getEtudiants() as $etudiant) {
            /** @var Stage $stage */
            foreach ($etudiant->getStages() as $stage) {
                $affectation = $stage->getAffectationStage();
                if ($affectation && $affectation->hasEtatValidee()) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function getAnneeUniversitaire() : ?AnneeUniversitaire
    {
        $id = intval($this->getParam('anneeUniversitaire'));
        return $this->getObjectManager()->getRepository(AnneeUniversitaire::class)->find($id);
    }

    protected function getGroupe() : ?Groupe
    {
        $id = intval($this->getParam('groupe'));
        return $this->getObjectManager()->getRepository(Groupe::class)->find($id);
    }
}