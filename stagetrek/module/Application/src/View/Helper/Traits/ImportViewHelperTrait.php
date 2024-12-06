<?php


namespace Application\View\Helper\Traits;

use Application\Form\Misc\ImportForm;
use Laminas\View\Renderer\RendererInterface as Renderer;

/**
 */

/**
 * Traits ImportViewHelperTrait
 * @package Application\View\Helper\Traits
 * @author Thibaut Vallée <thibaut.vallee at unicaen.fr>
 * Trait permettant d'afficher le formulaire d'importation de maniére générique
 * @method Renderer getView()
 */
trait ImportViewHelperTrait
{

    /**
     * @param ImportForm $form
     * @param string $importTemplateTitle
     * @param array $importTemplate
     * @return string
     */
    public function renderFormImportation($form, $importTemplateTitle=null, $importTemplate=null)
    {
        return $this->getView()->render("layout/templates/form-importation", [
            'form' => $form,
            'importTemplateTitle' => $importTemplateTitle,
            'importTemplate' => $importTemplate,
        ]);
    }

    /**
     * amélioration : fornir des options pour changer le label, l'icone, fournir un id au liens ...
     * !!! Ne permet pas en l'état d'avoir plusieur liens d'importation sur la même page
     * @param ImportForm $form
     * @return string
     */
    public function renderLienImportTemplate($templateTitle="modele_importation", $templateModele=null)
    {
        if(!isset($templateModele)||empty($templateModele)){
            return "";
        }
        $html = "<a class='import-template-link' id='import-template'>".PHP_EOL;
        $html .= "\t<span class='fa fa-file-csv m-1'></span>Modéle</a>".PHP_EOL;
        $html .= "</a>".PHP_EOL;
        $html .= PHP_EOL;
        return  $html;
    }

}