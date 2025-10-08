<?php


namespace Application\View\Helper\Parametres;

use Application\Controller\Parametre\ParametreCoutTerrainController as Controller;
use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Entity\Traits\Parametre\HasParametreCoutTerrainTrait;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ParametrePrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

/**
 * Class ParametreCoutTerrainViewHelper
 */
class ParametreCoutTerrainViewHelper extends AbstractEntityActionViewHelper
{
    use HasParametreCoutTerrainTrait;

    /**
     * @param ParametreTerrainCoutAffectationFixe|null $parametre
     * @return self
     */
    public function __invoke(?ParametreTerrainCoutAffectationFixe $parametre = null) : static
    {
        $this->parametreTerrainCoutAffectationFixe = $parametre;
        return $this;
    }
    /***********************************
     * Templates, Partial et Fragments *
     **********************************/
    function getNamespace(): string
    {
        return 'application/administration/parametre-cout-terrain';
    }


    public function renderListe(?array $entities = [], ?array $params=[]): string
    {
        $params = array_merge(['parametres' => $entities], $params);
        return $this->getView()->render('application/parametre/parametre-cout-terrain/listes/liste-parametres-couts-terrains',$params);
    }

    function renderForm(Form $form): string
    {
        return $this->getView()->render("application/parametre/parametre-cout-terrain/forms/form-parametre-cout-terrain", ['form' => $form]);
    }

    public function actionAllowed(string $action) : bool
    {
        return match ($action) {
            Controller::ACTION_AJOUTER => $this->hasPrivilege(ParametrePrivileges::PARAMETRE_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasParametreTerrainCoutAffectationFixe() && $this->hasPrivilege(ParametrePrivileges::PARAMETRE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasParametreTerrainCoutAffectationFixe() && $this->hasPrivilege(ParametrePrivileges::PARAMETRE_SUPPRIMER),
            default => false,
        };
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? Label::render(Label::AJOUTER, Icone::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter un coût fixe à un terrain";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $parametre = $this->getParametreTerrainCoutAffectationFixe();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['parametreTerrainCoutAffectationFixe' => $parametre->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier le coût fixe du terrain";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $parametre = $this->getParametreTerrainCoutAffectationFixe();
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['parametreTerrainCoutAffectationFixe' => $parametre->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le cout fixe du terrain";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
}