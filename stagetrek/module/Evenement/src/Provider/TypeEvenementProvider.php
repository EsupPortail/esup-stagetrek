<?php

namespace Evenement\Provider;

/**
 * Contient la liste des codes pour les templates des mails automatiques
 * @package Application\Provider\Privilege
 */
class TypeEvenementProvider
{
    const COLLECTION = 'collection';
    const MAIL = 'mail';
    const TEST = 'test';
    const MAIL_AUTO_STAGE_DEBUT_CHOIX = 'mail-auto-stage-debut-choix';
    const MAIL_AUTO_RAPPEL_STAGE_CHOIX = 'mail-auto-stage-rappel-choix';

    const MAIL_AUTO_AFFECTATION_VALIDEE = 'mail-auto-affectation-validee';
    const MAIL_AUTO_LISTE_ETUDIANTS_STAGES = 'mail-auto-liste-etudiants-stages';
    const MAIL_AUTO_DEBUT_VALIDATION_STAGE = 'mail-auto-stage-debut-validation';
    const MAIL_AUTO_RAPPEL_STAGE_VALIDATION = 'mail-auto-stage-rappel-validation';
    const MAIL_AUTO_STAGE_VALIDATION_EFFECTUE = 'mail-auto-stage-validation-effectue';
}

