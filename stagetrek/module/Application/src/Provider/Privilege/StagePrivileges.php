<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Class StagePrivileges
 * @package Application\Provider\Privilege
 */
class StagePrivileges extends Privileges
{
    const STAGE_AFFICHER = 'stage-stage_afficher';
    const STAGE_AJOUTER = 'stage-stage_ajouter';
    const STAGE_MODIFIER = 'stage-stage_modifier';
    const STAGE_SUPPRIMER = 'stage-stage_supprimer';

    const ETUDIANT_OWN_STAGES_AFFICHER = 'stage-etudiant_own_stages_afficher';


    const VALIDATION_STAGE_AFFICHER = 'stage-validation_stage_afficher';
    const VALIDATION_STAGE_MODIFIER = 'stage-validation_stage_modifier';

//    TODO  : revoir pour les priviléges de validation/pré-validation
//    TODO : revoir pour l'action d'export
    const AFFECTATION_AFFICHER = 'stage-affectation_afficher';
    const AFFECTATION_AJOUTER = 'stage-affectation_ajouter';
    const AFFECTATION_MODIFIER = 'stage-affectation_modifier';
    const AFFECTATION_SUPPRIMER = 'stage-affectation_supprimer';
    const AFFECTATION_RUN_PROCEDURE = 'stage-affectation_run_procedure';
    const AFFECTATION_PRE_VALIDER = 'stage-affectation_pre_valider';
    const COMMISSION_VALIDER_AFFECTATIONS = 'stage-commission_valider_affectations';

    const PROCEDURE_AFFICHER = 'stage-procedure_afficher';
    const PROCEDURE_MODIFIER = 'stage-procedure_modifier';

}