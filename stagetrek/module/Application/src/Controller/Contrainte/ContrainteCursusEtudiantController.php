<?php /** @noinspection ALL */

namespace Application\Controller\Contrainte;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Entity\Db\Etudiant;
use Application\Entity\Traits\Contraintes\HasContrainteCursusTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Form\Contrainte\Traits\ContrainteCursusFormAwareTrait;
use Application\Service\Contrainte\Traits\ContrainteCursusEtudiantServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * Class ContrainteCursusEtudiantController
 * @package Application\Controller
 * @method FlashMessenger flashMessenger()
 * @method ZfcUserAuthentication zfcUserAuthentication()
 */
class ContrainteCursusEtudiantController extends AbstractActionController
{
    use HasEtudiantTrait;
    use HasContrainteCursusTrait;
    use EtudiantServiceAwareTrait;
    use ContrainteCursusEtudiantServiceAwareTrait;
    use ContrainteCursusFormAwareTrait;

    const ROUTE_LISTER = 'etudiant/lister-contraintes';
    const ROUTE_AFFICHER = 'etudiant/cursus/contrainte/afficher';
    const ROUTE_MODIFIER = 'etudiant/cursus/contrainte/modifier';
    const ROUTE_VALIDER = 'etudiant/cursus/contrainte/valider';
    const ROUTE_INVALIDER = 'etudiant/cursus/contrainte/invalider';
    const ROUTE_ACTIVER = 'etudiant/cursus/contrainte/activer';
    const ROUTE_DESACTIVER = 'etudiant/cursus/contrainte/desactiver';

    const ACTION_LISTER = "lister";
    const ACTION_AFFICHER = 'afficher';
    const ACTION_MODIFIER = 'modifier';
    const ACTION_VALIDER = 'valider';
    const ACTION_INVALIDER = 'invalider';
    const ACTION_ACTIVER = 'activer';
    const ACTION_DESACTIVER = 'desactiver';

    const EVENT_MODIFIER = "event-modifier-contrainte-cursus-etudiant";

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction() : ViewModel
    {
        $etudiant = $this->getEtudiantFromRoute();
        return new ViewModel(['etudiant' => $etudiant]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAction() : ViewModel
    {
        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $this->getContrainteCursusEtudiantFromRoute();
        $title = sprintf("Fiche de la contrainte %s pour %s", $contrainte->getLibelle(), $contrainte->getEtudiant()->getDisplayName());

        return new ViewModel(['title' => $title, 'contrainte'=>$contrainte]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAction() : ViewModel
    {
        $title = "Modifier la contrainte";

        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $this->getContrainteCursusEtudiantFromRoute();

        $form = $this->getContrainteCursusEtudiantForm();
        $form->bind($contrainte);
        $form->setEtudiantContrainteCursus($contrainte);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ContrainteCursusEtudiant $contrainte */
                    $contrainte = $form->getData();
                    $contrainte = $this->getContrainteCursusEtudiantService()->update($contrainte);
                    $msg = "La contrainte a été modifiée";
                    $this->sendSuccessMessage($msg);
                    $form->bind($contrainte);
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
    public function validerAction() : ViewModel
    {
        $title = "Valider manuellement la contrainte";
        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $this->getContrainteCursusEtudiantFromRoute();


        /** @var Etudiant $etudiant */
        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vraiment valider la contrainte %s pour %s ?",
            $contrainte->getLibelle(),
            $contrainte->getEtudiant()->getDisplayName()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getContrainteCursusEtudiantService()->validerContrainte($contrainte);
                $msg = "La contrainte a été validée";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function invaliderAction() : ViewModel
    {
        $title = "Invalider la contrainte";
        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $this->getContrainteCursusEtudiantFromRoute();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vraiment invalider la contrainte %s pour %s ?",
            $contrainte->getLibelle(),
            $contrainte->getEtudiant()->getDisplayName()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getContrainteCursusEtudiantService()->invaliderContrainte($contrainte);
                $msg = "La contrainte a été invalidée";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function activerAction() : ViewModel
    {
        $title = "Activer la contrainte";
        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $this->getContrainteCursusEtudiantFromRoute();

        /** @var Etudiant $etudiant */
        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vraiment activer cette contrainte pour %s ?",
            $contrainte->getEtudiant()->getDisplayName()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getContrainteCursusEtudiantService()->activerContrainte($contrainte);
                $msg = "La contrainte a été activée.";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function desactiverAction() : ViewModel
    {
        $title = "Désactiver la contrainte";
        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $this->getContrainteCursusEtudiantFromRoute();

        /** @var Etudiant $etudiant */
        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vraiment que %s n'est plus à satisfaire cette contrainte?",
            $contrainte->getEtudiant()->getDisplayName()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getContrainteCursusEtudiantService()->desactiverContrainte($contrainte);
                $msg = "La contrainte a été désactivée.";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }
}