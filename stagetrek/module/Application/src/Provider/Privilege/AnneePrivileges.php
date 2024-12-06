<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Class StagePrivileges
 * @package Application\Provider\Privilege
 */
class AnneePrivileges extends Privileges
{
    const ANNEE_UNIVERSITAIRE_AFFICHER = 'annee-annee_universitaire_afficher';
    const ANNEE_UNIVERSITAIRE_AJOUTER = 'annee-annee_universitaire_ajouter';
    const ANNEE_UNIVERSITAIRE_MODIFIER = 'annee-annee_universitaire_modifier';
    const ANNEE_UNIVERSITAIRE_SUPPRIMER = 'annee-annee_universitaire_supprimer';
    const ANNEE_UNIVERSITAIRE_VALIDER = 'annee-annee_universitaire_valider';
    const ANNEE_UNIVERSITAIRE_DEVERROUILLER = 'annee-annee_universitaire_deverrouiller';
}