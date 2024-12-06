<?php


namespace Application\Controller\Preference;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\Preference;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Preference\HasPreferenceTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Form\Preferences\Traits\PreferenceFormAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\Service\Preference\Traits\PreferenceServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use Exception;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * Class PreferenceController
 * @package Application\Controller\Etudiants
 *
 * @method ZfcUserAuthentication zfcUserAuthentication()
 */
class PreferenceController extends AbstractActionController
{
    //Entity
    use ParametreServiceAwareTrait;
    use EtudiantServiceAwareTrait;
    use PreferenceServiceAwareTrait;
    use HasPreferenceTrait;
    use StageServiceAwareTrait;
    use HasValidationStageTrait;
    use PreferenceFormAwareTrait;

    /*********************************************************
     ** Partie portant sur l'administration des préférences **
     *********************************************************/

    /** ROUTES */
    const ROUTE_LISTER = 'preferences/lister';
    const ROUTE_MODIFIER_PREFERENCES = 'preferences/modifier';
    const ROUTE_AJOUTER = 'preference/ajouter';
    const ROUTE_MODIFIER = 'preference/modifier';
    const ROUTE_MODIFIER_RANG = 'preference/modifier-rang';
    const ROUTE_SUPPRIMER = 'preference/supprimer';
    const ROUTE_LISTER_PLACES = 'preference/lister-places';

    /** ACTIONS */
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_MODIFIER_RANG = "modifier-rang";
    const ACTION_MODIFIER_PREFERENCES = "modifier-preferences";
    const ACTION_SUPPRIMER = "supprimer";
    const ACTION_LISTER_PLACES = "lister-places";

    /** EVENTS */
    const EVENT_AJOUTER = "event-ajouter-preference";
    const EVENT_MODIFIER = "event-modifier-preference";
    const EVENT_SUPPRIMER = "event-supprimer-preference";

    /*****************
     ** Les actions **
     ****************
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction() : ViewModel
    {
        $stage = $this->getStageFromRoute();
        return new ViewModel(['stage' => $stage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierPreferencesAction() : ViewModel
    {
        $stage = $this->getStageFromRoute();

        return new ViewModel(['stage' => $stage, 'vueEtudiante' => $this->currentRoleIsStudent()]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter une préférence";
        /** @var Stage $stage */
        $stage = $this->getStageFromRoute();
        $preference = $this->getPreferenceService()->getNewPrefence($stage);
        $form = $this->getAddPreferenceForm();
        $modeAdmin = ($this->getEtudiantFromUser() === null);
        $form->setModeAdmin($modeAdmin);
        $form->setPreference($preference);
        $form->bind($preference);

        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var Preference $preference */
                    $preference = $form->getData();
                    $this->getPreferenceService()->add($preference);
                    $msg = "La préférence a été ajouté";
                    $this->sendSuccessMessage($msg);
                    $preference = $this->getPreferenceService()->getNewPrefence($stage);
                    $form->bind($preference);
                    $form->setPreference($preference);
//                    return $this->ajouterAction();
//                    $form->bind(null);
//                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'stage' => $stage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAction(): ViewModel
    {
        $title = "Modifier la préférence";
        /** @var Preference $preference */
        $preference = $this->getPreferenceFromRoute();
        $stage = $preference->getStage();

        $form = $this->getEditPreferenceForm();
        $modeAdmin = ($this->getEtudiantFromUser() === null);
        $form->setModeAdmin($modeAdmin);
        $form->setPreference($preference);
        $form->bind($preference);

        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var Preference $preference */
                    $preference = $form->getData();
                    $this->getPreferenceService()->update($preference);
                    $msg = "La préférence a été modifiée";
                    $this->sendSuccessMessage($msg);
                    $form->bind($preference);
                    $form->setPreference($preference);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'stage' => $stage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function supprimerAction(): ViewModel
    {
        $title = "Supprimer la préférence";
        /** @var Preference $preference */
        $preference = $this->getPreferenceFromRoute();

        $preferenceService =  $this->getPreferenceService();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment supprimer la préférence de rang %s ?",
            $preference->getRang()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $preferenceService->delete($preference);
                $msg = "La préférence a été supprimée";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerPlacesAction(): ViewModel
    {
        $stage = $this->getStageFromRoute();
        return new ViewModel(['stage' => $stage]);
    }


    /**
     * @return JsonModel
     */
    protected function modifierRangAction(): JsonModel
    {
        $json = ['value' => 1];
        try {
            $rang = (int)$this->params()->fromRoute('rang');
            $preference = $this->getPreferenceFromRoute();
            $preference->setRang($rang);
            $this->getPreferenceService()->update($preference);
            $preferences = $this->getPreferenceService()->findAllBy(['stage' =>$preference->getStage()]);
            /** @var Preference $pref */
            foreach ($preferences as $pref) {
                $json["preferences"][] = [
                    'preferenceId' => $pref->getId(),
                    'rang' => $pref->getRang(),
                    'terrainId' => $pref->getTerrainStage()->getId(),
                    'terrain' => $pref->getTerrainStage()->getLibelle(),
                ];
            }
        }
        catch (RuntimeException|Exception $e){
            $json['value']=0;
            $json['error']=$e->getMessage();

        }
        return new JsonModel($json);
    }

}