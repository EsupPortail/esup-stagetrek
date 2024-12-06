<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Liste des privilèges utilisables.
 */
class MessagePrivilege extends Privileges
{
    const MESSAGE_INFO_AFFICHER = 'message-message_info_afficher';
    const MESSAGE_INFO_AJOUTER = 'message-message_info_ajouter';
    const MESSAGE_INFO_MODIFIER = 'message-message_info_modifier';
    const MESSAGE_INFO_SUPPRIMER = 'message-message_info_supprimer';
}