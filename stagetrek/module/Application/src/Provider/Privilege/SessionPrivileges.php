<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Class StagePrivileges
 * @package Application\Provider\Privilege
 */
class SessionPrivileges extends Privileges
{

    const SESSION_STAGE_AFFICHER = 'session-session_stage_afficher';
    const SESSION_STAGE_AJOUTER = 'session-session_stage_ajouter';
    const SESSION_STAGE_MODIFIER = 'session-session_stage_modifier';
    const SESSION_STAGE_SUPPRIMER = 'session-session_stage_supprimer';
}