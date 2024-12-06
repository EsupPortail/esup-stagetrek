<?php

namespace Application\Controller\Parametre;


use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\NiveauEtude;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Form\Parametre\Traits\NiveauEtudeFormsAwareTrait;
use Application\Service\Parametre\Traits\NiveauEtudeServiceAwareTrait;
use Exception;
use Laminas\View\Model\ViewModel;

/**
 * Class NiveauEtudeController
 * @package Application\Controller
 */
class NiveauEtudeController extends AbstractActionController
{
    const ROUTE_INDEX = 'parametre/niveau-etude';
    const ROUTE_LISTER = 'parametre/niveau-etude/lister';
    const ROUTE_AJOUTER = "parametre/niveau-etude/ajouter";
    const ROUTE_MODIFIER = "parametre/niveau-etude/modifier";
    const ROUTE_SUPPRIMER = "parametre/niveau-etude/supprimer";

    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = 'ajouter';
    const ACTION_MODIFIER = 'modifier';
    const ACTION_SUPPRIMER = 'supprimer';

    const EVENT_AJOUTER = "event-ajouter-niveau-etude";
    const EVENT_MODIFIER= "event-modifier-niveau-etude";
    const EVENT_SUPPRIMER = "event-supprimer-niveau-etude";


    use NiveauEtudeServiceAwareTrait;
    use ConfirmationFormAwareTrait;
    use NiveauEtudeFormsAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction(): ViewModel
    {
        $niveaux = $this->getNiveauEtudeService()->findAll();
        return new ViewModel(['niveaux' => $niveaux]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $niveaux = $this->getNiveauEtudeService()->findAll();
        return new ViewModel(['niveaux' => $niveaux]);
    }

    public function ajouterAction(): ViewModel
    {
        $title = "Ajouter un niveau d'étude";
        $form = $this->getAddNiveauEtudeForm();
        $form->bind(new NiveauEtude());
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var NiveauEtude $niveau */
                    $niveau = $form->getData();
                    $this->getNiveauEtudeService()->add($niveau);
                    $msg = sprintf("Le niveau d'étude %s a été ajouté",
                        $niveau->getLibelle()
                    );
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAction(): ViewModel
    {
        $title = "Modifier le niveau d'étude";
        /** @var NiveauEtude $niveau */
        $niveau = $this->getNiveauEtudeFromRoute();

        $service = $this->getNiveauEtudeService();
        $form = $this->getEditNiveauEtudeForm();
        $form->fixerNiveauEtude($niveau);
        $form->bind($niveau);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var NiveauEtude $niveau */
                    $niveau = $form->getData();
                    $service->update($niveau);
                    $msg = sprintf("Le niveau d'étude %s a été modifié.",
                        $niveau->getLibelle()
                    );
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function supprimerAction(): ViewModel
    {
        $title = "Supprimer le niveau d'étude";
        /** @var NiveauEtude $niveau */
        $niveau = $this->getNiveauEtudeFromRoute();
        $service = $this->getNiveauEtudeService();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment supprimer le niveau d'étude %s ?",
            $niveau->getLibelle()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $service->delete($niveau);
                $msg = sprintf("Le niveau d'étude %s a été supprimée.",
                    $niveau->getLibelle()
                );
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }
}