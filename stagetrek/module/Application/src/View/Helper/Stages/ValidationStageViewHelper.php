<?php

namespace Application\View\Helper\Stages;

use Application\Controller\Stage\ValidationStageController as Controller;
use Application\Entity\Db\Stage;
use Application\Entity\Db\ValidationStage;
use Application\Entity\Traits\Stage\HasStageTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\EtatType\ValidationStageEtatTypeProvider;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\StagePrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Application\View\Helper\Interfaces\EtudiantActionViewHelperInterface;
use Application\View\Helper\Interfaces\Implementation\EtudiantActionViewHelperTrait;
use DateTime;
use Laminas\Form\Form;
use UnicaenEtat\Service\EtatInstance\EtatInstanceServiceAwareTrait;
use UnicaenEtat\Service\EtatType\EtatTypeServiceAwareTrait;

/**
 * Class StageViewHelper
 * @package Application\View\Helper\Stages
 */
class ValidationStageViewHelper extends AbstractEntityActionViewHelper
implements EtudiantActionViewHelperInterface
{
    use EtudiantActionViewHelperTrait;
    use HasValidationStageTrait;
    use HasStageTrait;
    use EtatTypeServiceAwareTrait;

    /**
     * @param ValidationStage|null $validationStage
     * @return self
     */
    public function __invoke(?ValidationStage $validationStage = null): static
    {
        $this->validationStage = $validationStage;
        if(isset($validationStage)){
            $this->stage = $validationStage->getStage();
        }
        return $this;
    }

    public function render() : string
    {
        $vueEtudiante = $this->vueEtudianteActive();
        $stage =  $this->getStage();
        if($vueEtudiante){ //Changement fictif de l'état validé / invalidé pour les étudiants si l'on est en dehors des délais
            $validation = $stage->getValidationStage();
            $etat = $validation->getEtatActif();
            $today = new DateTime();
            if(isset($etat) &&  $today < $stage->getDateFinStage()
                && ($validation->hasEtatValide() || $validation->hasEtatInvalide())
            ){
                $codeType = ($today < $stage->getDateDebutValidation()) ? ValidationStageEtatTypeProvider::FUTUR : ValidationStageEtatTypeProvider::EN_ATTENTE;
                $typeFictif = $this->getEtatTypeService()->getEtatTypeByCode($codeType);
                $etat->setType($typeFictif);
            }
        }

        $params = ['stage' => $stage, 'vueEtudiante' => $vueEtudiante];
        return $this->getView()->render('application/stage/validation-stage/partial/fiche-validation', $params);
    }

    /**
     * @param Form $form
     * @return string
     */
    public function renderForm(Form $form) : string
    {
        $params = ['stage' => $this->getStage(), 'form' => $form];
        return $this->getView()->render('application/stage/validation-stage/forms/form-validation-stage', $params);
    }


    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action): bool
    {
        $ressources = new ArrayRessource();
        if($this->hasStage()){$ressources->add(Stage::RESOURCE_ID, $this->getStage());}
       return match ($action) {
            Controller::ACTION_AFFICHER => $this->hasStage() && $this->hasPrivilege(StagePrivileges::VALIDATION_STAGE_AFFICHER),
            Controller::ACTION_MODIFIER => $this->hasStage() && $this->callAssertion($ressources, StagePrivileges::VALIDATION_STAGE_MODIFIER),
            default => false,
        };
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['stage' => $this->getStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ?? "Modifier la validation du stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

}