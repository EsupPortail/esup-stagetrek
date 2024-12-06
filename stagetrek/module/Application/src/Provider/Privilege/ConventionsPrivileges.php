<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Class ConventionsStagesPrivileges
 * @package Application\Provider\Privilege
 */
class ConventionsPrivileges extends Privileges
{
    const CONVENTION_AFFICHER = 'convention-convention_afficher';
    const CONVENTION_TELEVERSER = 'convention-convention_televerser';
    const CONVENTION_GENERER = 'convention-convention_generer';
    const CONVENTION_MODIFIER = 'convention-convention_modifier';
    const CONVENTION_SUPPRIMER = 'convention-convention_supprimer';
    const CONVENTION_TELECHARGER = 'convention-convention_telecharger';
    const MODELE_CONVENTION_AFFICHER = 'convention-modele_convention_afficher';
    const MODELE_CONVENTION_AJOUTER = 'convention-modele_convention_ajouter';
    const MODELE_CONVENTION_MODIFIER = 'convention-modele_convention_modifier';
    const MODELE_CONVENTION_SUPPRIMER = 'convention-modele_convention_supprimer';

}