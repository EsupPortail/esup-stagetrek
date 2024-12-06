<?php

namespace Fichier\View\Helper;

use Fichier\Entity\Db\Fichier;
use UnicaenApp\Filter\BytesFormatter;
use Laminas\View\Helper\AbstractHelper;

class FichierViewHelper extends AbstractHelper
{

    /**
     * @return $this
     */
    public function __invoke() : FichierViewHelper
    {
        return $this;
    }

    public function __call(String $name, array $arguments = []) : FichierViewHelper
    {
        $attr = call_user_func_array([null, $name], $arguments);
        return $this;
    }

    /**
     * @param Fichier $fichier
     * @return string
     */
    public function render(Fichier $fichier, $retour = null) : string
    {
        $text = "";
        $text .= "<div class='fichier-view-helper'>";
        $text .= "<div class='row'>";
        $text .= "<div class='col-md-6'>";
        $text .= "<dl class='dl-horizontal'>";
        $text .= "<dt>Nom du fichier</dt>";
        $text .= "<dd>". $fichier->getNomOriginal() . "</dd>";
        $text .= "<dt>Taille du fichier</dt>";
        $text .= "<dd>". (new BytesFormatter())->filter($fichier->getTaille())."</dd>";
        $text .= "<dt>Déposé</dt>";
        $text .= "<dd>". $fichier->getHistoModification()->format('d/m/Y à H:i') . " par " . $fichier->getHistoModificateur()->getDisplayName() ."</dd>";
        $text .= "</dl>";
        $text .= "</div>";
        $text .= "<div class='col-md-6'>";
        $text .= "<a href='".$this->getView()->url('fichier/download', ['fichier' => $fichier->getId()], [], true)."' class='btn btn-success action pull-right'>";
        $text .= "<span class='icon icon-download'></span> Télécharger le fichier </a>";
        $text .= "<a href='".$this->getView()->url('fichier/supprimer', ['fichier' => $fichier->getId()], [ 'query' => ['retour' => $retour]], true)."'";
        $text .= " class='btn btn-danger action pull-right'>";
        $text .= " <span class='icon icon-retirer text-danger'></span> Effacer le fichier </a>";
        $text .= "</div>";
        $text .= "</div>";
        $text .= "</div>";

        return $text;
    }
}