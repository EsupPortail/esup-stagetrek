<?php


namespace Application\Form\Abstrait\Interfaces;


/**
 * Interface AbstractFormConstantesInterface
 * @package Application\Form\Abstrait\Interfaces
 */
interface AbstractFormConstantesInterface
{
    // Liste des input générique
    const INPUT_SUBMIT = "submit";
    const INPUT_ID = "id";
    const INPUT_CODE = "code";
    const INPUT_LIBELLE = "libelle";
    const INPUT_DESCRIPTION = "description";
    const INPUT_RECHERCHER = "rechercher";

    /** Id/Name des inputs */
    const INPUT_RECHERCHE_CODE = "rechercheCode";
    const INPUT_RECHERCHE_LIBELLE = "rechercheLibelle";
    /** Libellés des inputs */
    const LABEL_RECHERCHE_CODE = "Code";
    const LABEL_RECHERCHE_LIBELLE = "Libellé";
    /** Placeholder des inputs */
    const PLACEHOLDER_RECHERCHE_CODE = "Rechercher un code";
    const PLACEHOLDER_RECHERCHE_LIBELLE = "Rechercher un libellé";


    //Pour le controle de session
    const CSRF ="csrf";
    const SUBMIT ='submit';

    // Liste des label
    const LABEL_SUBMIT_CONFIRM = "<i class='fas fa-save'></i> Valider";
    const LABEL_SUBMIT_ADD = "<i class='fas fa-save'></i> Ajouter";
    const LABEL_SUBMIT_IMPORT = "<i class='fas fa-download'></i> Importer";
    const LABEL_SUBMIT_EDIT = "<i class='fas fa-save'></i> Modifier";
    const LABEL_SUBMIT_RECHERCHER = "<i class='fas fa-search'></i> Rechercher";
    const LABEL_LIBELLE = "Libellé ";
    const LABEL_DESCRIPTION = "Description";

    //Place holder
    const PLACEHOLDER_LIBELLE = "Saisir un libellé";

    //Template des messages d'erreur
    const INVALIDE_ERROR_MESSAGE = "Le formulaire n'est pas valide :";

}