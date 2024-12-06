<?php

namespace Application\Provider\EtatType;
/**
 * Contient la liste des codes pour les templates des mails automatiques
 * @package Application\Provider\Privilege
 */
class AffectationEtatTypeProvider
{
    const CODE_CATEGORIE = 'affectation';

    const FUTUR =   self::CODE_CATEGORIE . "_" . "futur";
    const EN_COURS =   self::CODE_CATEGORIE . "_" . "en_cours";
    const EN_RETARD =   self::CODE_CATEGORIE . "_" . "en_retard";
    const PROPOSTION =   self::CODE_CATEGORIE . "_" . "proposition";
    const PRE_VAlIDEE =   self::CODE_CATEGORIE . "_" . "pre_valide";
    const VAlIDEE =   self::CODE_CATEGORIE . "_" . "valide";
    const EN_AlERTE =   self::CODE_CATEGORIE . "_" . "en_alerte";
    const EN_ERREUR =   self::CODE_CATEGORIE . "_" . "en_erreur";
    const STAGE_NON_EFFECTUE =   self::CODE_CATEGORIE . "_" . "stage_non_effectue";
    const EN_DISPO =   self::CODE_CATEGORIE . "_" . "en_disponibilite";
    //cas d'un stage non tagué comme non effecuté, mais dont l'étudiant n'as pas d'affectation
    // Si le délai est dépassé, une affectation sans terrains est considéré comme un stage non effectué, mais avec une légére différence sémantique
    const NON_AFFECTE =   self::CODE_CATEGORIE . "_" . "non_affecte";
}
