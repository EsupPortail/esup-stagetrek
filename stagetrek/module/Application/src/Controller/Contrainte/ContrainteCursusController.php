<?php


namespace Application\Controller\Contrainte;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ContrainteCursus;
use Application\Entity\Traits\Contraintes\HasContrainteCursusTrait;
use Application\Form\Contrainte\Traits\ContrainteCursusFormAwareTrait;
use Application\Service\Contrainte\Traits\ContrainteCursusServiceAwareTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * Class ContrainteCursusEtudiantController
 * @package Application\Controller
 * @method FlashMessenger flashMessenger()
 * @method ZfcUserAuthentication zfcUserAuthentication()
 */
class ContrainteCursusController extends AbstractActionController
{
    use HasContrainteCursusTrait;
    use ContrainteCursusServiceAwareTrait;
    use ContrainteCursusFormAwareTrait;

    const ROUTE_INDEX = 'config/contrainte-cursus';
    const ROUTE_LISTER = 'config/contraintes-cursus/lister';
    const ROUTE_AJOUTER = 'config/contrainte-cursus/ajouter';
    const ROUTE_MODIFIER = 'config/contrainte-cursus/modifier';
    const ROUTE_SUPPRIMER = 'config/contrainte-cursus/supprimer';

    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";

    const EVENT_AJOUTER = "event-ajouter-contrainte-cursus";
    const EVENT_MODIFIER = "event-modifier-contrainte-cursus";
    const EVENT_SUPPRIMER = "event-supprimer-contrainte-cursus";

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction() : ViewModel
    {
        $contraintes = $this->getContrainteCursusService()->findAll();
        return new ViewModel(['contraintes' => $contraintes]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $contraintes = $this->getContrainteCursusService()->findAll();
        return new ViewModel(['contraintes' => $contraintes]);
    }

    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter une contraintes";
        $form = $this->getAddContrainteCursusForm();
        $form->bind(new ContrainteCursus());
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ContrainteCursus $contrainte */
                    $contrainte = $form->getData();
                    $this->getContrainteCursusService()->add($contrainte);
                    $msg = sprintf("La contrainte %s a été ajoutée",
                        $contrainte->getLibelle()
                    );
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAction(): ViewModel
    {
        $title = "Modifier la contraintes";
        /** @var ContrainteCursus $contrainte */
        $contrainte = $this->getContrainteCursusFromRoute();

        $service = $this->getContrainteCursusService();
        $form = $this->getEditContrainteCursusForm();
        $form->bind($contrainte);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ContrainteCursus $contrainte */
                    $contrainte = $form->getData();
                    $service->update($contrainte);
                    $msg = sprintf("La contrainte %s a été modifiée",
                        $contrainte->getLibelle()
                    );
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function supprimerAction(): ViewModel
    {
        $title = "Supprimer la contrainte";
        /** @var ContrainteCursus $contrainte */
        $contrainte = $this->getContrainteCursusFromRoute();
        $service = $this->getContrainteCursusService();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment supprimer la contrainte %s ?",
            $contrainte->getLibelle()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $service->delete($contrainte);
                $msg = sprintf("La contrainte %s a été supprimée.",
                    $contrainte->getLibelle()
                );
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }

        return new ViewModel(['title' => $title, 'form'=>$form]);
    }
}