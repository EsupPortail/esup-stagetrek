<?php

namespace Evenement\View\Helper;

use Evenement\Provider\TypeEvenementProvider;
use Application\View\Renderer\PhpRenderer;
use Laminas\View\Helper\Partial;
use Laminas\View\Resolver\TemplatePathStack;
use UnicaenEvenement\Provider\Privilege\EvenementtypePrivileges;
use UnicaenPrivilege\Provider\Privilege\Privileges;

class TypeViewHelper extends \UnicaenEvenement\View\Helper\TypeViewHelper
{

    /**
     * @return string|Partial
     */
    public function render(): string|Partial
    {
        if($this->type == null) {
            return '';
        }

        /** @var PhpRenderer $view */
        $view = $this->getView();
        $view->resolver()->attach(new TemplatePathStack(['script_paths' => [__DIR__ . "/partial"]]));

        return $view->partial('type', ['type' => $this->type, 'icon' => $this->renderIcon()]);
    }


    /**
     * @return string
     */
    public function renderIcon() : string
    {
        $type = $this->getType();
        if(!isset($type)){return "";}
        $class = match ($type->getCode()) {
            TypeEvenementProvider::COLLECTION => "fas fa-bars",
            TypeEvenementProvider::TEST => "fas fa-check-square",
            TypeEvenementProvider::MAIL,
            TypeEvenementProvider::MAIL_AUTO_STAGE_DEBUT_CHOIX,
            TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_CHOIX,
            TypeEvenementProvider::MAIL_AUTO_AFFECTATION_VALIDEE,
            TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE,
            TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_VALIDATION,
            TypeEvenementProvider::MAIL_AUTO_STAGE_VALIDATION_EFFECTUE,
            TypeEvenementProvider::MAIL_AUTO_LISTE_ETUDIANTS_STAGES
                => 'fas fa-envelope',
            default => "far fa-question-circle",
        };

        return sprintf('<span class="%s"></span>', $class);
    }

    /**
     * @return string
     */
    public function renderLienAjouter()
    {


        if(!$this->view->isAllowed(Privileges::getResourceId(EvenementTypePrivileges::TYPE_AJOUT))){
            return "";
        }
        return sprintf(
            '<a href="%s" 
                class="btn btn-sm btn-success ajax-modal"
                title="%s"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                data-bs-html ="true"
                data-event="%s",
                >%s %s</a>',
            $this->view->url('unicaen-evenement/type/ajouter', [], [], true),
            $this->view->escapeHtml("Ajouter un type d'événement"),
            "event-evenement-type-ajouter",
            "<span class='icon icon-ajouter'></span>",
            "Ajouter"
        );
    }

    /**
     * @return string
     */
    public function renderLienModifier()
    {

        if(!$this->view->isAllowed(Privileges::getResourceId(EvenementTypePrivileges::TYPE_EDITION))){
            return "";
        }
        return sprintf(
            '<a href="%s" 
                class="btn btn-sm btn-primary ajax-modal"
                title="%s"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                data-bs-html ="true"
                data-event="%s",
                >%s</a>',
            $this->view->url('unicaen-evenement/type/modifier', ['type' => $this->type->getId()], [], true),
            $this->view->escapeHtml("Modifier le type d'évéenement"),
            "event-evenement-type-modifier",
            "<span class='icon icon-modifier'></span>",
        );
    }

    /**
     * @return string
     */
    public function renderLienSupprimer()
    {

        if(!$this->view->isAllowed(Privileges::getResourceId(EvenementTypePrivileges::TYPE_SUPPRESSION))){
            return "";
        }
        return sprintf(
            '<a href="%s" 
                class="btn btn-sm btn-danger ajax-modal"
                title="%s"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                data-bs-html ="true"
                data-event="%s",
                >%s</a>',
            $this->view->url('unicaen-evenement/type/supprimer', ['type' => $this->type->getId()], [], true),
            $this->view->escapeHtml("Supprimer le type d'évéenement"),
            "event-evenement-type-supprimer",
            "<span class='icon icon-supprimer'></span>"
        );
    }
}