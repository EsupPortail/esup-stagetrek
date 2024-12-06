<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Class FaqPrivileges
 * @package Application\Provider\Privilege
 */
class FaqPrivileges extends Privileges
{
    const FAQ_QUESTION_MODIFIER = 'faq-faq_question_modifier';
    const FAQ_QUESTION_SUPPRIMER = 'faq-faq_question_supprimer';
    const FAQ_CATEGORIE_AFFICHER = 'faq-faq_categorie_afficher';
    const FAQ_QUESTION_AFFICHER = 'faq-faq_question_afficher';
    const FAQ_QUESTION_AJOUTER = 'faq-faq_question_ajouter';
    const FAQ_CATEGORIE_SUPPRIMER = 'faq-faq_categorie_supprimer';
    const FAQ_CATEGORIE_MODIFIER = 'faq-faq_categorie_modifier';
    const FAQ_CATEGORIE_AJOUTER = 'faq-faq_categorie_ajouter';
}

