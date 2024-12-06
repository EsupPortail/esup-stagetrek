<?php

namespace Application\Provider\EtatType;
/**
 * Contient la liste des codes pour les templates des mails automatiques
 * @package Application\Provider\Privilege
 */
class StageEtatTypeProvider
{
    const CODE_CATEGORIE = 'stage';

    const FUTUR =   self::CODE_CATEGORIE . "_" . "futur";
    const PHASE_PREFERENCE =   self::CODE_CATEGORIE . "_" . "preference";
    const PHASE_AFFECTATION =   self::CODE_CATEGORIE . "_" . "affectation";
//    Phase entre les affectation et le début du stage
// Différent de futur qui est "avant les choix"
    const A_VENIR =   self::CODE_CATEGORIE . "_" . "a_venir";
    const EN_COURS =   self::CODE_CATEGORIE . "_" . "en_cours";
    const PHASE_VALIDATION =   self::CODE_CATEGORIE . "_" . "validation";
    const VALIDATION_EN_RETARD =   self::CODE_CATEGORIE . "_" . "validation_retard";
    const PHASE_EVALUATION =   self::CODE_CATEGORIE . "_" . "evaluation";
    const EVALUATION_EN_RETARD =   self::CODE_CATEGORIE . "_" . "evaluation_retard";
    const TERMINE_VALIDE =   self::CODE_CATEGORIE . "_" . "termine_valide";
    const TERMINE_NON_VALIDE =   self::CODE_CATEGORIE . "_" . "termine_non_valide";
//    Autres cas
    const EN_AlERTE =   self::CODE_CATEGORIE . "_" . "en_alerte";
    const EN_ERREUR =   self::CODE_CATEGORIE . "_" . "en_erreur";
    const NON_EFFECTUE =   self::CODE_CATEGORIE . "_" . "non_effectue";
    const EN_DISPO =   self::CODE_CATEGORIE . "_" . "en_disponibilite";
    const DESACTIVE =   self::CODE_CATEGORIE . "_" . "desactive";

}
