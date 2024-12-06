<?php

namespace Application\Provider\EtatType;
/**
 * Contient la liste des codes pour les templates des mails automatiques
 * @package Application\Provider\Privilege
 */
class ContrainteCursusEtudiantEtatTypeProvider
{
    const CODE_CATEGORIE = 'contrainte_cursus';
    const SAT =   self::CODE_CATEGORIE . "_" . "sat";
    const VALIDE_COMMISSION =   self::CODE_CATEGORIE . "_" . "valide";
    const NON_SAT =   self::CODE_CATEGORIE . "_" . "non_sat";
    const INSAT =   self::CODE_CATEGORIE . "_" . "insat";
    const WARNING =   self::CODE_CATEGORIE . "_" . "warning";
    const EN_ERREUR =   self::CODE_CATEGORIE . "_" . "en_erreur";
    const DESACTIVE =   self::CODE_CATEGORIE . "_" . "desactive";
}
