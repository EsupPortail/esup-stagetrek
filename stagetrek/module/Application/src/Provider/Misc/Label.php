<?php

namespace Application\Provider\Misc;

class Label
{
    const AFFICHER = "Afficher";
    const AJOUTER = "Ajouter";
    const MODIFIER = "Modifier";
    const SUPPRIMER = "Supprimer";
    const IMPORTER = "Importer";
    const EXPORTER = "Exporter";
    const RETIRER = "Retirer";
    const TELECHARGER = "Télécharger";
    const TELEVERSER = "Téléverser";
    const VALIDER = "Valider";
    const AFFECTER = "Affecter";

    /** Formate le libellé avec possiblement une icone devant */
    public static function render(string $label, string $icone =null, $title=null) : string
    {
        if(isset($icone)){
            $icone = Icone::render($icone)." ";
        }
        return sprintf("<span title='%s'>%s%s", $title, $icone, $label);
    }

    const LABEL_AJOUTER = "<span class='".Icone::AJOUTER."'></i> Ajouter";
    const LABEL_MODIFIER = "<span class='".Icone::MODIFIER."'></i> Modifier";
    const LABEL_SUPPRIMER = "<span class='".Icone::SUPPRIMER."'></i> Supprimer";
}