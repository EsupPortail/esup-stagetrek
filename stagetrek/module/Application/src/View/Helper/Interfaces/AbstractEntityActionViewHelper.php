<?php


namespace Application\View\Helper\Interfaces;

use Application\View\Helper\Interfaces\Implementation\ActionsLinkerTrait;
use Exception;
use Laminas\Form\Form;
use Laminas\View\Helper\AbstractHelper;

/**
 * @package Application\View\Helper\Interfaces
 * @author Thibaut Vallée <thibaut.vallee at unicaen.fr>
 */
abstract Class AbstractEntityActionViewHelper extends AbstractHelper
implements EntityActionViewHelperInterface
{
    use ActionsLinkerTrait;

    /**
     * @throws \Exception
     */
    public function __toString() : string
    {
        return $this->render();
    }

    public function render() : string
    {
        throw new Exception("Fonction render non implémentée");
    }

    /***********************************
     * Templates, Partial et Fragments *
     **********************************/
    /** @deprecated a remplacer */
    function renderListeView(string $name, array $params): string
    {
        $path = sprintf("%s/listes/%s", $this->getNamespace(), $name);
        return $this->getView()->render($path, $params);
    }

    /** @deprecated a remplacer */
    function renderFormView(string $name, array $params): string
    {
        $path = sprintf("%s/forms/%s", $this->getNamespace(), $name);
        return $this->getView()->render($path, $params);
    }

    /** @deprecated a remplacer */
    function renderPartialView(string $name, array $params): string
    {
        $path = sprintf("%s/partial/%s", $this->getNamespace(), $name);
        return $this->getView()->render($path, $params);
    }

    /** @return string */
    function renderListe(?array $entities = [], ?array $params = []) : string
    {
        throw new Exception("Fonction renderListe non implémentée");
    }

    function renderForm(Form $form): string
    {
        throw new Exception("Fonction renderForm non implémentée");
    }

    /**************************
     * Liens pour les actions *
     **************************/


    //TODO : a Remplacer au fur et a mesure par generateActionLink

    /** @deprecated a remplacer */
    function renderActionLink(string $action, string $label=null, string $url=null, string $icon=null, array  $attributes=[],  string $notAllowedRender=null): string
    {
        if(! $this->actionAllowed($action)){
            return ($notAllowedRender) ?? "";
        }
        $content = trim(sprintf("%s %s",$icon, $label));
        $attr = "";
        foreach ($attributes as $key => $value){
            $attr .= sprintf("%s='%s' ",$key, $this->view->escapeHtml($value));
        }
        $href = ($url != "") ? sprintf("href='%s'", $url) : "";
        $html = sprintf("<a %s %s>%s</a>", $href, $attr, $content);
        return $html;
    }


    /** @deprecated a remplacer */
    function getNamespace(): string
    {
        throw new Exception("Fonction getNamespace non implémentée");
    }
    /**
     * @desc met les liens vers les actions dans un menu déroulant
     * !!! $actions doit contenir les liens vers les actions
     */

    /** @deprecated a remplacer */
    protected function genererateTableActions(array $actions = []) : string
    {
        if(empty($actions)){return "";}
        $html = "<div class='dropdown'>";
        $html .= "<button class='btn btn-sm' type='button'
        data-bs-toggle='dropdown'
        aria-expanded='false'>
        <span class ='fas fa-bars'>
        </button>";

        $html .= "<ul class='dropdown-menu'>";
        foreach ($actions as $actionLink){
            $html .= $actionLink;
        }
        $html .= "</ul>";
        $html .= "</div>";
        return $html;
    }
}