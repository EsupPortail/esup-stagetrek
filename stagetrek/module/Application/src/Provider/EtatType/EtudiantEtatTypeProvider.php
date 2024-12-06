<?php

namespace Application\Provider\EtatType;
/**
 * Contient la liste des codes pour les templates des mails automatiques
 * @package Application\Provider\Privilege
 */
class EtudiantEtatTypeProvider
{
    const CODE_CATEGORIE = 'etudiant';
    const CURSUS_EN_CONSTRUCTION =   self::CODE_CATEGORIE . "_" . "en_construction";
    const EN_AlERTE =   self::CODE_CATEGORIE . "_" . "en_alerte";
    const EN_ERREUR =   self::CODE_CATEGORIE . "_" . "en_erreur";
    const CURSUS_EN_COURS =   self::CODE_CATEGORIE . "_" . "cursus_en_cours";
    const CURSUS_VAlIDE =   self::CODE_CATEGORIE . "_" . "cursus_valide";
    const CURSUS_INVALIDE =   self::CODE_CATEGORIE . "_" . "cursus_invalide";
    const EN_DISPO =   self::CODE_CATEGORIE . "_" . "dispo";
}
