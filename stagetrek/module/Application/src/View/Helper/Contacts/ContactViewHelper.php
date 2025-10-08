<?php


namespace Application\View\Helper\Contacts;

use Application\Controller\Contact\ContactController as Controller;
use Application\Entity\Db\Contact;
use Application\Entity\Traits\Contact\HasContactTrait;
use Application\Form\Contacts\ContactRechercheForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ContactPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

class ContactViewHelper  extends AbstractEntityActionViewHelper
{
    use HasContactTrait;

    /**
     * @param Contact|null $contact
     * @return self
     */
    public function __invoke(Contact $contact = null): static
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['contacts' => $entities], $params);
        return $this->getView()->render('application/contact/contact/listes/liste-contacts', $params);
    }


    function renderForm(Form $form): string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/contact/contact/forms/form-contact', $params);
    }

    function renderRechercheForm(ContactRechercheForm $form): string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/contact/contact/forms/form-recherche-contact', $params);
    }
    function renderInfos(): string
    {
        $params = ['contact' => $this->getContact()];
        return $this->getView()->render('application/contact/contact/partial/contact-infos', $params);
    }

    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasContact()){$ressources->add(Contact::RESOURCE_ID, $this->getContact());}
        return match ($action) {
            Controller::ACTION_AFFICHER => $this->hasContact() && $this->hasPrivilege(ContactPrivileges::CONTACT_AFFICHER),
            Controller::ACTION_AJOUTER => $this->hasPrivilege(ContactPrivileges::CONTACT_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasContact() && $this->hasPrivilege(ContactPrivileges::CONTACT_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasContact() && $this->hasPrivilege(ContactPrivileges::CONTACT_SUPPRIMER),
            Controller::ACTION_IMPORTER => $this->hasPrivilege(ContactPrivileges::CONTACT_IMPORTER),
            default => false,
        };
    }

    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if(!$this->hasContact()){
            return "";
        }
        $libelle = ($libelle) ?? sprintf("%s", $this->getContact()->getCode());
        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {
            return $libelle;
        }
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['contact' => $this->getContact()->getId()], [], true);

        $attributes['title'] = ($attributes['title']) ??  "Fiche du contact";
        $attributes['class'] = ($attributes['class']) ?? "text-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? Label::render(Label::AJOUTER, Icone::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter un contact";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        if(!isset($attributes['data-event'])){$attributes['data-event'] = Controller::EVENT_AJOUTER;}
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['contact' => $this->getContact()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier le contact";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['contact' => $this->getContact()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le contact";
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
        $libelle = ($libelle) ?? Label::render(Label::IMPORTER, Icone::IMPORTER);
        $attributes['title'] = ($attributes['title']) ??  "Importer des contacts";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        if(!isset($attributes['data-event'])){$attributes['data-event'] = Controller::EVENT_IMPORTER;}
        return $this->generateActionLink($url, $libelle, $attributes);
    }
}