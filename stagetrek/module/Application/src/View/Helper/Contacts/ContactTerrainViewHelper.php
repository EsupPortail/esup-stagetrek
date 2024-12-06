<?php


namespace Application\View\Helper\Contacts;

use Application\Controller\Contact\ContactTerrainController as Controller;
use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\TerrainStage;
use Application\Entity\Traits\Contact\HasContactTerrainTrait;
use Application\Entity\Traits\Contact\HasContactTrait;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ContactPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

class ContactTerrainViewHelper extends AbstractEntityActionViewHelper
{
    use HasContactTrait;
    use HasContactTerrainTrait;
    use HasTerrainStageTrait;

    /**
     * @param ContactTerrain|null $contactTerrain
     * @return self
     */
    public function __invoke(ContactTerrain $contactTerrain = null): static
    {
        $this->contactTerrain = $contactTerrain;
        return $this;
    }

    /***********************************
     * Templates, Partial et Fragments *
     **********************************/
    function getNamespace(): string
    {
        return 'application/contact/contact-terrain';
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['contact' => $this->getContact(), 'contactsTerrains' => $entities], $params);
        return $this->getView()->render('application/contact/contact-terrain/listes/liste-contacts-terrains', $params);
    }


    function renderForm(Form $form): string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/contact/contact-terrain/forms/form-contact-terrain', $params);
    }

    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasContact()){$ressources->add(Contact::RESOURCE_ID, $this->getContact());}
        if($this->hasContactTerrain()){$ressources->add(ContactTerrain::RESOURCE_ID, $this->getContactTerrain());}
        if($this->hasTerrainStage()){$ressources->add(TerrainStage::RESOURCE_ID, $this->getTerrainStage());}
        return match ($action) {
            Controller::ACTION_AJOUTER => $this->callAssertion($ressources, ContactPrivileges::CONTACT_TERRAIN_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasContactTerrain() && $this->hasPrivilege(ContactPrivileges::CONTACT_TERRAIN_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasContactTerrain() && $this->hasPrivilege(ContactPrivileges::CONTACT_TERRAIN_SUPPRIMER),
            Controller::ACTION_IMPORTER =>  $this->hasPrivilege(ContactPrivileges::CONTACT_TERRAIN_IMPORTER),
            default => false,
        };
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $contact = $this->getContact();
        $terrain = $this->getTerrainStage();
        $data =['contact' => (isset($contact)) ? $contact->getId() : 0, 'terrainStage' => (isset($terrain)) ? $terrain->getId() : null];
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, $data, [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::AJOUTER, Label::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter un contact de terrain";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['contactTerrain' => $this->getContactTerrain()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier le contact du terrain";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER,  ['contactTerrain' => $this->getContactTerrain()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le contact du terrain";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienImporter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_IMPORTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_IMPORTER, [], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::IMPORTER, Label::IMPORTER);
        $attributes['title'] = ($attributes['title']) ??  "Importer des contacts de terrains";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        if(!isset($attributes['data-event'])){$attributes['data-event'] = Controller::EVENT_IMPORTER;}
        return $this->generateActionLink($url, $libelle, $attributes);
    }
}