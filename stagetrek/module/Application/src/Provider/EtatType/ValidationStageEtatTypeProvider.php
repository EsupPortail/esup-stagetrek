<?php

namespace Application\Provider\EtatType;
/**
 * Contient la liste des codes pour les templates des mails automatiques
 * @package Application\Provider\Privilege
 */
class ValidationStageEtatTypeProvider
{
    const CODE_CATEGORIE = 'validation_stage';

    const FUTUR =   self::CODE_CATEGORIE . "_" . "futur";
    const EN_ATTENTE =   self::CODE_CATEGORIE . "_" . "en_attente";
    const EN_RETARD =   self::CODE_CATEGORIE . "_" . "en_retard";
    const VALIDE =   self::CODE_CATEGORIE . "_" . "valide";
    const INVAlIDE =   self::CODE_CATEGORIE . "_" . "invalide";
    const STAGE_NON_EFFECTUE =   self::CODE_CATEGORIE . "_" . "stage_non_effectue";
    const EN_DISPO =   self::CODE_CATEGORIE . "_" . "en_disponibilite";
    const EN_ALERTE =   self::CODE_CATEGORIE . "_" . "en_alerte";
    const EN_ERREUR =   self::CODE_CATEGORIE . "_" . "en_erreur";
}
