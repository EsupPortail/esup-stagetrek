<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Class AdministrationPrivileges
 * @package Application\Provider\Privilege
 */
class ParametrePrivileges extends Privileges
{

    const PARAMETRE_AFFICHER = "parametre-parametre_afficher";
    const PARAMETRE_AJOUTER = "parametre-parametre_ajouter";
    const PARAMETRE_MODIFIER = "parametre-parametre_modifier";
    const PARAMETRE_SUPPRIMER = "parametre-parametre_supprimer";

    const NIVEAU_ETUDE_AFFICHER = "parametre-niveau_etude_afficher";
    const NIVEAU_ETUDE_AJOUTER = "parametre-niveau_etude_ajouter";
    const NIVEAU_ETUDE_MODIFIER = "parametre-niveau_etude_modifier";
    const NIVEAU_ETUDE_SUPPRIMER = "parametre-niveau_etude_supprimer";

    const PARAMETRE_CONTRAINTE_CURSUS_AFFICHER = "parametre-parametre_contrainte_cursus_afficher";
    const PARAMETRE_CONTRAINTE_CURSUS_AJOUTER = "parametre-parametre_contrainte_cursus_ajouter";
    const PARAMETRE_CONTRAINTE_CURSUS_MODIFIER = "parametre-parametre_contrainte_cursus_modifier";
    const PARAMETRE_CONTRAINTE_CURSUS_SUPPRIMER = "parametre-parametre_contrainte_cursus_supprimer";
}