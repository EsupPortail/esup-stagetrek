<?php


namespace Application\View\Helper\Parametres;

use Application\Controller\Parametre\NiveauEtudeController as Controller;
use Application\Entity\Db\NiveauEtude;
use Application\Entity\Traits\Parametre\HasNiveauEtudeTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ParametrePrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

/**
 * Class NiveauEtudeViewHelper
 * @package Application\View\Helper\Referentiels
 */
class NiveauEtudeViewHelper extends AbstractEntityActionViewHelper
{
    use HasNiveauEtudeTrait;

    /**
     * @param \Application\Entity\Db\NiveauEtude|null $niveauEtude
     * @return self
     */
    public function __invoke(?NiveauEtude $niveauEtude = null): static
    {
        $this->niveauEtude = $niveauEtude;
        return $this;
    }

    public function renderListe(?array $entities = [], ?array $params=[]): string
    {
        $params = array_merge(['niveaux' => $entities], $params);
        return $this->getView()->render('application/parametre/niveau-etude/listes/liste-niveaux-etudes',$params);
    }

    function renderForm(Form $form): string
    {
        return $this->getView()->render("application/parametre/niveau-etude/forms/form-niveau-etude", ['form' => $form]);
    }

    public function actionAllowed(string $action) : bool
    {
        $ressource = new ArrayRessource();
        if($this->hasNiveauEtude()){$ressource->add(NiveauEtude::RESOURCE_ID, $this->getNiveauEtude());}

        return match ($action) {
            Controller::ACTION_AJOUTER => $this->hasPrivilege(ParametrePrivileges::NIVEAU_ETUDE_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasNiveauEtude() && $this->hasPrivilege(ParametrePrivileges::NIVEAU_ETUDE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasNiveauEtude() && $this->callAssertion($ressource, ParametrePrivileges::NIVEAU_ETUDE_SUPPRIMER),
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
        $attributes['title'] = ($attributes['title']) ?? "Ajouter un niveau d'étude";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $niveau = $this->getNiveauEtude();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['niveauEtude' => $niveau->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ?? "Modifier le niveau d'étude";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $niveau = $this->getNiveauEtude();
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['niveauEtude' => $niveau->getId()], [], true);
        $libelle = Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le niveau d'étude";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
}