<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Class StagePrivileges
 * @package Application\Provider\Privilege
 */
class ContactPrivileges extends Privileges
{
    const CONTACT_AFFICHER =            'contact-contact_afficher';
    const CONTACT_AJOUTER =             'contact-contact_ajouter';
    const CONTACT_MODIFIER =            'contact-contact_modifier';
    const CONTACT_SUPPRIMER =           'contact-contact_supprimer';
    const CONTACT_IMPORTER =           'contact-contact_importer';
    const CONTACT_EXPORTER =           'contact-contact_exporter';
    const CONTACT_STAGE_AFFICHER =      'contact-contact_stage_afficher';
    const CONTACT_STAGE_AJOUTER =       'contact-contact_stage_ajouter';
    const CONTACT_STAGE_MODIFIER =      'contact-contact_stage_modifier';
    const CONTACT_STAGE_SUPPRIMER =     'contact-contact_stage_supprimer';
    const CONTACT_TERRAIN_AFFICHER =    'contact-contact_terrain_afficher';
    const CONTACT_TERRAIN_AJOUTER =     'contact-contact_terrain_ajouter';
    const CONTACT_TERRAIN_MODIFIER =    'contact-contact_terrain_modifier';
    const CONTACT_TERRAIN_SUPPRIMER =   'contact-contact_terrain_supprimer';
    const CONTACT_TERRAIN_IMPORTER =   'contact-contact_terrain_importer';
    const CONTACT_TERRAIN_EXPORTER =   'contact-contact_terrain_exporter';

    const SEND_MAIL_VALIDATION =        'contact-send_mail_validation';
}