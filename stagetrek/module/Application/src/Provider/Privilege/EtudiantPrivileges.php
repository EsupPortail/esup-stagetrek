<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Class EtudiantPrivileges
 * @package Application\Provider\Privilege
 */
class EtudiantPrivileges extends Privileges
{
    const ETUDIANT_AFFICHER = 'etudiant-etudiant_afficher';
    const ETUDIANT_MODIFIER = 'etudiant-etudiant_modifier';
    const ETUDIANT_AJOUTER = 'etudiant-etudiant_ajouter';
    const ETUDIANT_SUPPRIMER = 'etudiant-etudiant_supprimer';
    const GROUPE_AFFICHER = 'etudiant-groupe_afficher';
    const GROUPE_MODIFIER = 'etudiant-groupe_modifier';
    const GROUPE_AJOUTER = 'etudiant-groupe_ajouter';
    const GROUPE_SUPPRIMER = 'etudiant-groupe_supprimer';
    const GROUPE_ADMINISTRER_ETUDIANTS = 'etudiant-groupe_administrer_etudiants';

//    Privilége spécifique requis car ils s'agit d'actions pour les étudiants
    const ETUDIANT_OWN_PROFIL_AFFICHER = 'etudiant-etudiant_own_profil_afficher';
    const ETUDIANT_OWN_PREFERENCES_AFFICHER = 'etudiant-etudiant_own_preferences_afficher';
    const ETUDIANT_OWN_PREFERENCES_AJOUTER = 'etudiant-etudiant_own_preferences_ajouter';
    const ETUDIANT_OWN_PREFERENCES_MODIFIER = 'etudiant-etudiant_own_preferences_modifier';
    const ETUDIANT_OWN_PREFERENCES_SUPPRIMER = 'etudiant-etudiant_own_preferences_supprimer';

    const PREFERENCE_AFFICHER = 'etudiant-preference_afficher';
    const PREFERENCE_AJOUTER = 'etudiant-preference_ajouter';
    const PREFERENCE_MODIFIER = 'etudiant-preference_modifier';
    const PREFERENCE_SUPPRIMER = 'etudiant-preference_supprimer';

    const DISPONIBILITE_AFFICHER = 'etudiant-disponibilite_afficher';
    const DISPONIBILITE_AJOUTER = 'etudiant-disponibilite_ajouter';
    const DISPONIBILITE_MODIFIER = 'etudiant-disponibilite_modifier';
    const DISPONIBILITE_SUPPRIMER = 'etudiant-disponibilite_supprimer';
    const DISPONIBILITE_IMPORTER = 'etudiant-disponibilite_importer';

    const ETUDIANT_CONTRAINTES_AFFICHER = 'etudiant-etudiant_contraintes_afficher';
    const ETUDIANT_CONTRAINTE_MODIFIER = 'etudiant-etudiant_contrainte_modifier';
    const ETUDIANT_CONTRAINTE_VALIDER = 'etudiant-etudiant_contrainte_valider';
    const ETUDIANT_CONTRAINTE_INVALIDER = 'etudiant-etudiant_contrainte_invalider';
    const ETUDIANT_CONTRAINTE_ACTIVER = 'etudiant-etudiant_contrainte_activer';
    const ETUDIANT_CONTRAINTE_DESACTIVER = 'etudiant-etudiant_contrainte_desactiver';

    public static function getActionResourceId($controller, $action = null, $param=[])
    {
        if (isset($action)) {
            return sprintf('controller/%s:%s', $controller, strtolower($action));
        }

        return sprintf('controller/%s', $controller);
    }
}