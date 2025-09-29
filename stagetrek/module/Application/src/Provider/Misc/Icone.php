<?php

namespace Application\Provider\Misc;
class Icone
{
    const FA_AWARD = "fas fa-award";
    const FA_CALENDAR = "fas fa-calendar";
    const FA_CERTIFCATE = "fas fa-certificate";
    const FA_PROCESS = "fas fa-gears";
    const FA_CHECK = "fas fa-check";
    const FA_CHECK_SLOT = "fas fa-check-to-slot";
    const FA_CHECK_LIST = "fas fa-list-check";
    const FA_CHECK_SQUARE = "fas fa-square-check";
    const FA_DOUBLE_CHECK = "fas fa-check-double";
    const FA_PROGRESS_BAR ="fas fa-bars-progress";
    const FA_1 = "fas fa-1";
    const FA_2 = "fas fa-2";
    const FA_FLAG = "fas fa-flag";
    const FA_SAVE = "fas fa-save";
    const FA_USER = "fas fa-user";
    const FA_USERS = "fas fa-users";
    const FA_LOCK = "fas fa-lock";
    const FA_UNLOCK = "fas fa-lock-open";
    const FA_COGS = "fas fa-cogs";
    const FA_BAN = "fas fa-ban";
    const FA_TIMES = "fas fa-ban";
    const FA_CLOCK = "fas fa-clock";
    const FA_PLAY = "fas fa-clock";
    const FA_PAUSE = "fas fa-pause";
    const FA_HOURGLASS = "fas fa-hourglass";
    const FA_SPINNER = "fas fa-spinner";
    const FA_CIRCLE_XMARK = "fas fa-circle-xmark";
    const FA_CIRCLE_QUESTION = "fas fa-circle-question";
    const FA_EXCLATION_TRIANGLE = "fas fa-exclamation-triangle";
    const FA_EXCLATION_CIRCLE = "fas fa-exclamation-circle";
    const FA_INFO_CIRCLE = "fas fa-info-circle";

    const ETUDIANT = self::FA_USER;
    const GROUPE = self::FA_USERS;
    const SESSION_STAGE = "fas fa-briefcase-medical";
    const STAGE = "fas fa-notes-medical";
    const ANNEE = "fas fa-calendar";
    const AFFECTATION = self::FA_FLAG;
    const CHOIX = self::FA_CHECK_SLOT;
    const TERRAIN = "fas fa-house-medical";
    const CATEGORIE_STAGE = "fas fa-hospital";
    const CONTACT = "fas fa-user-doctor";
    const ETAT = self::FA_INFO_CIRCLE;



    const SUCCESS = Icone::FA_CHECK;
    const WARNING = Icone::FA_EXCLATION_TRIANGLE;
    const ERROR = Icone::FA_EXCLATION_TRIANGLE;
    const INFO = Icone::FA_EXCLATION_CIRCLE;

    const VALIDE = Icone::FA_CHECK;
    const NON_VALIDE = Icone::FA_TIMES;

    const ANNULE = Icone::FA_TIMES;
    const FUTUR = Icone::FA_CLOCK;
    const EN_COURS = Icone::FA_PLAY;
    const EN_PAUSE = Icone::FA_PAUSE;
    const EN_ATTENTE = Icone::FA_HOURGLASS;
    const TERMINE = Icone::FA_CHECK;
    const INDETERMINE = Icone::FA_CIRCLE_QUESTION;


    const AJOUTER = "icon icon-ajouter";
    const MODIFIER = "icon icon-modifier";
    const SUPPRIMER = "icon icon-supprimer";
    const AFFICHER = "icon icon-voir";
    const IMPORTER = "icon icon-importer";
    const EXPORTER = "icon icon-exporter";
    const RETIRER = "icon icon-retirer";
    const TELECHARGER = "icon icon-telecharger";
    const TELEVERSER = "icon icon-televerser";
    const PRECEDENT = "fas fa-chevron-left";


    const SUIVANT = "fas fa-chevron-right";
    const SAVE = "fas fa-save";
    const CHECK = "fas fa-check";
    const RUN_PROCESS = "icon icon-execute";
    const CONFIGURER = "fas fa-tools";
    const MAIL = "icon icon-mail";
    const LISTE = "fas fa-list";
    const MENU = "fas fa-ellipsis-h";
    const PDF = "fas fa-file-pdf";

    public static function render(string $icone, $complement=null, $title=null) : string
    {
        return sprintf("<span class='%s %s' title='%s'></span>", $icone, $complement, $title);
    }

}