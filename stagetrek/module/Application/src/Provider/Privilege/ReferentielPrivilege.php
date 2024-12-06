<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Class ReferentielPrivileges
 * @package Application\Provider\Privilege
 */
class ReferentielPrivilege extends Privileges
{
    const REFERENTIEL_SOURCE_AFFICHER = 'referentiel-source_afficher';
    const REFERENTIEL_SOURCE_AJOUTER = 'referentiel-source_ajouter';
    const REFERENTIEL_SOURCE_MODIFIER = 'referentiel-source_modifier';
    const REFERENTIEL_SOURCE_SUPPRIMER = 'referentiel-source_supprimer';

    const REFERENTIEL_PROMO_AFFICHER = 'referentiel-promo_afficher';
    const REFERENTIEL_PROMO_AJOUTER = 'referentiel-promo_ajouter';
    const REFERENTIEL_PROMO_MODIFIER = 'referentiel-promo_modifier';
    const REFERENTIEL_PROMO_SUPPRIMER = 'referentiel-promo_supprimer';
}

