<?php

namespace Application\Provider\EtatType;
/**
 * Contient la liste des codes pour les templates des mails automatiques
 * @package Application\Provider\Privilege
 */
class AnneeEtatTypeProvider
{
    const CODE_CATEGORIE = 'annee';
    const EN_CONSTRUCTION =   self::CODE_CATEGORIE . "_" . "en_construction";
    const NON_VAlIDE =   self::CODE_CATEGORIE . "_" . "non_valide";
    const FURTUR =      self::CODE_CATEGORIE . "_" . "futur";
    const EN_COURS =    self::CODE_CATEGORIE . "_" . "en_cours";
    const TERMINE =     self::CODE_CATEGORIE . "_" . "termine";
}

