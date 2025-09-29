<?php


namespace Application\View\Helper\Contacts;

use Application\Controller\Contact\ContactStageController as Controller;
use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Contact\HasContactStageTrait;
use Application\Entity\Traits\Contact\HasContactTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ContactPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use DateTime;
use Laminas\Form\Form;

class ContactStageViewHelper extends AbstractEntityActionViewHelper
{
    use HasStageTrait;
    use HasEtudiantTrait;
    use HasContactTrait;
    use HasContactStageTrait;
    use HasValidationStageTrait;

    /**
     * @param ContactStage|null $contactStage
     * @return self
     */
    public function __invoke(ContactStage $contactStage = null): static
    {
        $this->contactStage = $contactStage;
        return $this;
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['contactsStages' => $entities, 'contact' => $this->getContact()], $params);
        return $this->getView()->render('application/contact/contact-stage/listes/liste-contacts-stages', $params);
    }

    function renderForm(Form $form): string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/contact/contact-stage/forms/form-contact-stage', $params);
    }


    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasContact()){$ressources->add(Contact::RESOURCE_ID, $this->getContact());}
        if($this->hasContactStage()){$ressources->add(ContactStage::RESOURCE_ID, $this->getContactStage());}
        if($this->hasStage()){$ressources->add(Stage::RESOURCE_ID, $this->getStage());}
        return match ($action) {
            Controller::ACTION_AJOUTER => $this->callAssertion($ressources, ContactPrivileges::CONTACT_STAGE_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasContactStage() && $this->hasPrivilege(ContactPrivileges::CONTACT_STAGE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasContactStage() && $this->hasPrivilege(ContactPrivileges::CONTACT_STAGE_SUPPRIMER),
            Controller::ACTION_SEND_MAIL_VALIDATION => $this->hasContactStage() && $this->callAssertion($ressources, ContactPrivileges::SEND_MAIL_VALIDATION),
            default => false,
        };
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $contact = $this->getContact();
        $stage =  $this->getStage();
        $data =['contact' => ($contact) ? $contact->getId() : 0, ($stage) ? $stage->getId() : null];
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, $data, [], true);

        $libelle = ($libelle) ?? Label::render(Label::AJOUTER, Icone::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter un contact de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['contactStage' => $this->getContactStage()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ?? "Modifier le lien entre le contact et le stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER,  ['contactStage' => $this->getContactStage()->getId()], [], true);
        $libelle = Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le contact du stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienMailValidation(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SEND_MAIL_VALIDATION)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SEND_MAIL_VALIDATION,  ['contactStage' => $this->getContactStage()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render("Lien de validation", Icone::MAIL);
        $attributes['title'] = ($attributes['title']) ?? "Envoyer un lien de validation pour le stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-sm btn-secondary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SEND_LIEN_VALIDATION;
        return $this->generateActionLink($url, $libelle, $attributes);
    }


    /********************
     * Autres fonctions *
     ********************/
    public function getTokenValidationEtatIcon(ContactStage $contactStage): string
    {
        $stage = $contactStage->getStage();
        $validation = $stage->getValidationStage();
        $token = $contactStage->getTokenValidation();
        $token = ($token) ?? "";
        $today = new DateTime();
        $mail = $contactStage->getEmail();

        $title = "<strong>Token de validation</strong>";
        switch (true) {
            case  (!$contactStage->canValiderStage()) :
                $icone = "<span class='text-muted icon icon-unchecked'></span>";
                $content = "<div>Le contact ne peux pas valider le stage.</div>";
            break;
            case  ($validation->validationEffectue()) :
                $icone = "<span class='text-primary icon icon-checked'></span>";
                $content = "<div>La validation a été effectuée</div>";
            break;
            case  (!isset($mail) || $mail=="" || !filter_var($mail, FILTER_VALIDATE_EMAIL)) :
                $icone = "<span class='text-danger icon icon-warning'></span>";
                $content = "<div>Le contact n'as pas une adresse mail autorisant la validation</div>";
            break;
            case ($token == "" && $today < $stage->getDateDebutValidation()) :
                $icone = "<span class='text-muted icon icon-unchecked'></span>";
                $content = sprintf("<div>La phase de validation est fixée au %s</div>", $stage->getDateDebutValidation()->format('d/m/Y'));
            break;
            case ($token == "" && $stage->getDateDebutValidation() < $today) :
                $icone = "<span class='text-warning icon icon-warning'></span>";
                $content = "<div>Le lien de validation n'as pas été définie</div>";
            break;
            case ($contactStage->getTokenExpirationDate() < $today) :
                $icone = "<span class='text-warning icon icon-warning'></span>";
                $content = "<div>Le lien de validation a expirée</div>";
            break;
            default :
                $icone = "<span class='text-success icon icon-checked'></span>";
                $content = sprintf("<div>Le lien de validation expire le %s</div>", $contactStage->getTokenExpirationDate()->format('d/m/Y'));
            break;
        }
        return sprintf('<a class="" 
            data-bs-toggle="popover" 
            data-bs-placement="bottom" 
            data-bs-trigger="focus"
            tabindex="0"
            data-bs-content="%s"
            title="%s"
            >
        %s</a>',
            $content,
            $title,
            $icone,
        );
    }
}