<?php


namespace Application\View\Helper\Parametres;

use Application\Controller\Parametre\ParametreCoutAffectationController as Controller;
use Application\Entity\Db\ParametreCoutAffectation;
use Application\Entity\Traits\Parametre\HasParametreCoutAffectationTrait;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ParametrePrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

/**
 * Class ParametreCoutAffectationViewHelper
 */
class ParametreCoutAffectationViewHelper extends AbstractEntityActionViewHelper
{
    use HasParametreCoutAffectationTrait;

    /**
     * @param \Application\Entity\Db\ParametreCoutAffectation|null $parametre
     * @return self
     */
    public function __invoke(?ParametreCoutAffectation $parametre = null) : static
    {
        $this->parametreCoutAffectation = $parametre;
        return $this;
    }


    public function renderListe(?array $entities = [], ?array $params=[]): string
    {
        $params = array_merge(['parametres' => $entities], $params);
        return $this->getView()->render('application/parametre/parametre-cout-affectation/listes/liste-parametres-couts-affectations',$params);
    }

    function renderForm(Form $form): string
    {
        return $this->getView()->render("application/parametre/parametre-cout-affectation/forms/form-parametre-cout-affectation", ['form' => $form]);
    }



    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action) : bool
    {
        return match ($action) {
            Controller::ACTION_AJOUTER => $this->hasPrivilege(ParametrePrivileges::PARAMETRE_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasParametreCoutAffectation() && $this->hasPrivilege(ParametrePrivileges::PARAMETRE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasParametreCoutAffectation() && $this->hasPrivilege(ParametrePrivileges::PARAMETRE_SUPPRIMER),
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
        $attributes['title'] = ($attributes['title']) ??  "Ajouter un coût d'affectation";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $parametre = $this->getParametreCoutAffectation();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['parametreCoutAffectation' => $parametre->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier le coût d'affectation";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $parametre = $this->getParametreCoutAffectation();
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['parametreCoutAffectation' => $parametre->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le cout d'affectation";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
}