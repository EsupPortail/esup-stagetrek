<?php

namespace Fichier\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

class FichierPrivileges extends Privileges
{
    const FICHIER_AFFICHER = 'fichier-fichier_afficher';
    const FICHIER_MODIFIER = 'fichier-fichier_modifier';
    const FICHIER_TELEVERSER = 'fichier-fichier_televerser';
    const FICHIER_TELECHARGER = 'fichier-fichier_telecharger';
    const FICHIER_ARCHIVER = 'fichier-fichier_archiver';
    const FICHIER_RESTAURER = 'fichier-fichier_restaurer';
    const FICHIER_SUPPRIMER = 'fichier-fichier_supprimer';
}