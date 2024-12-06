<?php

namespace Application\Provider\EtatType;
/**
 * Contient la liste des codes pour les templates des mails automatiques
 * @package Application\Provider\Privilege
 */
class SessionEtatTypeProvider
{
    const CODE_CATEGORIE = 'session';
    const DESACTIVE =   self::CODE_CATEGORIE . "_" . "desactive";
    const EN_AlERTE =   self::CODE_CATEGORIE . "_" . "en_alerte";
    const EN_ERREUR =   self::CODE_CATEGORIE . "_" . "en_erreur";
    const FUTUR =   self::CODE_CATEGORIE . "_" . "futur";
    const PHASE_PREFERENCE =   self::CODE_CATEGORIE . "_" . "preference";
    const PHASE_AFFECTATION =   self::CODE_CATEGORIE . "_" . "affectation";
//    Phase entre les affectation et le début du stage
// Différent de futur qui est "avant les choix"
    const A_VENIR =   self::CODE_CATEGORIE . "_" . "a_venir";
    const EN_COURS =   self::CODE_CATEGORIE . "_" . "en_cours";
    const PHASE_VALIDATION =   self::CODE_CATEGORIE . "_" . "validation";
    const PHASE_EVALUATION =   self::CODE_CATEGORIE . "_" . "evaluation";
    const TERMINE =   self::CODE_CATEGORIE . "_" . "termine";
}
