<?php

namespace Application\Controller\Etudiant;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Application\Entity\Traits\Etudiant\HasDisponibiliteTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Form\Etudiant\Traits\DisponibiliteFormAwareTrait;
use Application\Service\Etudiant\Traits\DisponibiliteServiceAwareTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;

/**
 * Class GroupeController
 * @package Application\Controller
 *
 * @method FlashMessenger flashMessenger()
 */
class DisponibiliteController extends AbstractActionController
{
    use HasEtudiantTrait;
    use HasDisponibiliteTrait;

    use DisponibiliteServiceAwareTrait;
    use DisponibiliteFormAwareTrait;

    /** ROUTES */
    const ROUTE_INDEX = 'disponibilite';
    const ROUTE_LISTER = 'disponibilite/lister';
    const ROUTE_AJOUTER = 'disponibilite/ajouter';
    const ROUTE_MODIFIER = 'disponibilite/modifier';
    const ROUTE_SUPPRIMER = 'disponibilite/supprimer';

    /** ACTIONS */
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = 'ajouter';
    const ACTION_MODIFIER = 'modifier';
    const ACTION_SUPPRIMER = 'supprimer';
    /** EVENTS */
    const EVENT_AJOUTER = "event-ajouter-disponibilite";
    const EVENT_MODIFIER = "event-modifier-disponibilite";
    const EVENT_SUPPRIMER = "event-supprimer-disponibilite";

    /*****************
     ** Les actions **
     *****************/

    public function listerAction() : ViewModel
    {
        $etudiant = $this->getEtudiantFromRoute();
        return new ViewModel(['etudiant' => $etudiant]);
    }

    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter une disponibilité";
        /** @var Etudiant $etudiant */
        $etudiant = $this->getEtudiantFromRoute();

        $dispo = new Disponibilite();
        $dispo->setEtudiant($etudiant);
        $form = $this->getAddDisponibiliteForm();
        $form->bind($dispo);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var Disponibilite $dispo */
                $dispo = $form->getData();
                try {
                    $this->getDisponibiliteService()->add($dispo);
                    $msg = "La disponibilité a été ajoutée.";
                    $this->sendSuccessMessage($msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form, 'etudiant'=>$etudiant]);
    }

    public function modifierAction(): ViewModel
    {
        $title = "Modifier la disponibilité";
        $dispo = $this->getDisponibiliteFromRoute();
        $etudiant = $dispo->getEtudiant();
        $form = $this->getEditDisponibiliteForm();
        $form->bind($dispo);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var Disponibilite $dispo */
                $dispo = $form->getData();
                try {
                    $msg = "La disponibilité a été modifiée.";
                    $dispo = $this->getDisponibiliteService()->update($dispo);
                    $this->sendSuccessMessage($msg);
                    $form->bind($dispo);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form, 'etudiant'=>$etudiant]);
    }

    public function supprimerAction(): ViewModel
    {
        $title = "Supprimer la disponibilité";

        /** @var Disponibilite $dispo */
        $dispo = $this->getDisponibiliteFromRoute();
        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vraiment supprimer la disponibilité de %s entre le %s et le %s?",
            $dispo->getEtudiant()->getDisplayName(),
            $dispo->getDateDebut()->format('d/m/Y'),
            $dispo->getDateFin()->format('d/m/Y')
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getDisponibiliteService()->delete($dispo);
                $msg = "La disponibilité a été supprimé.";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }
}