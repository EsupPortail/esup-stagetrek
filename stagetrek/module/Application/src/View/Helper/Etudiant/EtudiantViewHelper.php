<?php


namespace Application\View\Helper\Etudiant;

use Application\Controller\Etudiant\EtudiantController as Controller;
use Application\Entity\Db\Etudiant;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Validator\Import\EtudiantCsvImportValidator;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

/**
 * Class EtudiantViewHelper
 * @package Application\View\Helper\Etudiant
 */
class EtudiantViewHelper extends AbstractEntityActionViewHelper
{
    use EtudiantServiceAwareTrait;
    use HasEtudiantTrait;

    /**
     * @param Etudiant|null $etudiant
     * @return self
     */
    public function __invoke(Etudiant $etudiant = null): static
    {
        $this->etudiant = $etudiant;
        return $this;
    }



    function renderRechercheForm(Form $form): string
    {
        return $this->getView()->render("application/etudiant/etudiant/forms/form-recheche-etudiant", ['form' => $form]);
    }

    /**
     * @param Form $form
     * @return string
     */
    public function renderForm(Form $form): string
    {
        return $this->getView()->render("application/etudiant/etudiant/forms/form-etudiant", ['form' => $form]);
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['etudiants' => $entities], $params);
        return $this->getView()->render('application/etudiant/etudiant/listes/liste-etudiants', $params);
    }

    public function renderInfos() : string
    {
        if(!$this->hasEtudiant()){return "";}
        $params = ['etudiant' => $this->getEtudiant()];
        return $this->getView()->render('application/etudiant/etudiant/partial/etudiant-infos', $params);
    }

    public function renderListeStages() : string
    {
        if(!$this->hasEtudiant()){return "";}
        $params = ['etudiant' => $this->getEtudiant()];
        return $this->getView()->render('application/etudiant/etudiant/listes/liste-stages', $params);
    }

    /**
     * @param string $action
     * @return bool
     */
    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasEtudiant()){ $ressources->add(Etudiant::RESOURCE_ID, $this->getEtudiant());}
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER_STAGES,
                => $this->hasPrivilege(EtudiantPrivileges::ETUDIANT_AFFICHER),
            Controller::ACTION_AFFICHER => $this->hasEtudiant() && $this->hasPrivilege(EtudiantPrivileges::ETUDIANT_AFFICHER),
            Controller::ACTION_AJOUTER, Controller::ACTION_IMPORTER => $this->hasPrivilege(EtudiantPrivileges::ETUDIANT_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasEtudiant() && $this->hasPrivilege(EtudiantPrivileges::ETUDIANT_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasEtudiant() && $this->callAssertion($ressources, EtudiantPrivileges::ETUDIANT_SUPPRIMER),
            default => false,
        };
    }

    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if(!$this->hasEtudiant()){
            return "";
        }
        $libelle = ($libelle) ?? sprintf("%s", $this->getEtudiant()->getNumEtu());
        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {
            return $libelle;
        }
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['etudiant' => $this->getEtudiant()->getId()], [], true);

        $attributes['title'] = ($attributes['title']) ??  "Afficher l'étudiant";
        $attributes['class'] = ($attributes['class']) ?? "text-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::AJOUTER, Label::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter un étudiant";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $etudiant = $this->getEtudiant();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['etudiant' => $etudiant->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier les informations sur l'étudiant";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $etudiant = $this->getEtudiant();
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['etudiant' => $etudiant->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer l'étudiant";
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
        $libelle = ($libelle) ?? sprintf("%s Importer", Icone::IMPORTER);
        $attributes['title'] = ($attributes['title']) ?? "Importer des étudiants";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_IMPORTER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }


    public function getImportTemplate(): array
    {
        return [[
            EtudiantCsvImportValidator::HEADER_NUM_ETUDIANT => "123456789",
            EtudiantCsvImportValidator::HEADER_NOM => "John",
            EtudiantCsvImportValidator::HEADER_PRENOM => "Doe",
            EtudiantCsvImportValidator::HEADER_DATE_NAISSANCE => "01/01/1970",
            EtudiantCsvImportValidator::HEADER_EMAIL => "john.doe@mail.fr",
            EtudiantCsvImportValidator::HEADER_ADRESSE => "Une adresse",
            EtudiantCsvImportValidator::HEADER_ADRESSE_COMPLEMENT => "3éme étage",
            EtudiantCsvImportValidator::HEADER_CP => "14000",
            EtudiantCsvImportValidator::HEADER_VILLE => "Caen",
            EtudiantCsvImportValidator::HEADER_CEDEX => "Cedex 5",
        ]];
    }
}