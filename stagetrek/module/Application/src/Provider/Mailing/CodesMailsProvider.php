<?php

namespace Application\Provider\Mailing;

/**
 * Contient la liste des codes pour les templates des mails automatiques
 * @package Application\Provider\Privilege
 */
class CodesMailsProvider
{
    const STAGE_DEBUT_CHOIX = 'StageDebutChoix';
    const STAGE_DEBUT_CHOIX_RAPPEL = 'StageDebutChoix-Rappel';
    const AFFECTATION_STAGE_VALIDEE = 'AffectationStage-Validee';
    const VALIDATION_STAGE = 'ValidationStage';
    const VAlIDATION_STAGE_RAPPEL = 'ValidationStage-Rappel';
    const VALIDATION_STAGE_EFFECTUEE = 'ValidationStage-Effectuee';

    const MAIL_AUTO_VALIDATIONS_STAGES = 'MailAuto_ValidationsStages';
    const MAIL_AUTO_LISTE_ETUDIANTS_STAGES = 'MailAuto_ListeEtudiantsStages';
}

