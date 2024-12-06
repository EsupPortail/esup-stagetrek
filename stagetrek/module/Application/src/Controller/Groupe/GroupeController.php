<?php


namespace Application\Controller\Groupe;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Groupe;
use Application\Form\Groupe\Traits\GroupeFormAwareTrait;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use Application\Validator\Actions\Traits\GroupeValidatorAwareTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;

/**
 * Class GroupeController
 * @package Application\Controller\Etudiants
 *
 * @method FlashMessenger flashMessenger()
 */
class GroupeController extends AbstractActionController
{
    /** Accés aux entités */
    use GroupeServiceAwareTrait;
    use AnneeUniversitaireServiceAwareTrait;
    use EtudiantServiceAwareTrait;
    use GroupeFormAwareTrait;
    use GroupeValidatorAwareTrait;
    use ConfirmationFormAwareTrait;

    /** ROUTES */
    const ROUTE_INDEX = 'groupe';
    const ROUTE_AFFICHER = 'groupe/afficher';
    const ROUTE_AFFICHER_INFOS = 'groupe/afficher/infos';
    const ROUTE_AJOUTER = 'groupe/ajouter';
    const ROUTE_MODIFIER = 'groupe/modifier';
    const ROUTE_SUPPRIMER = 'groupe/supprimer';
    const ROUTE_LISTER_ETUDIANTS = 'groupe/etudiants/lister';
    const ROUTE_AJOUTER_ETUDIANTS = 'groupe/etudiants/ajouter';
    const ROUTE_RETIRER_ETUDIANTS = 'groupe/etudiants/retirer';

    //TODO : a revoir si réelement utile, a priori non
    const ROUTE_LISTER_SESSIONS = 'groupe/sessions/lister';


    /** ACTIONS */
    const ACTION_INDEX = "index";
    const ACTION_AFFICHER = "afficher";
    const ACTION_AFFICHER_INFOS = "afficher-infos";
    const ACTION_AJOUTER = 'ajouter';
    const ACTION_MODIFIER = 'modifier';
    const ACTION_SUPPRIMER = 'supprimer';
    const ACTION_LISTER_ETUDIANTS = "lister-etudiants";
    const ACTION_LISTER_SESSIONS = "lister-sessions";
    const ACTION_AJOUTER_ETUDIANTS = 'ajouter-etudiants';
    const ACTION_RETIRER_ETUDIANTS = 'retirer-etudiants';
    /** EVENTS */
    const EVENT_AJOUTER = "event-ajouter-groupe";
    const EVENT_MODIFIER = "event-modifier-groupe";
    const EVENT_SUPPRIMER = "event-supprimer-groupe";
    const EVENT_AJOUTER_ETUDIANTS = "event-ajouter-etudiants";
    const EVENT_RETIRER_ETUDIANTS = "event-retirer-etudiants";

    /*****************
     ** Les actions **
     ****************
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\DBAL\Exception
     */

    public function indexAction(): ViewModel
    {
        $form = $this->getGroupeRechercheForm();
        if ($data = $this->params()->fromQuery()) {
            $form->setData($data);
            $criteria = array_filter($data, function ($v) {
                return !empty($v);
            });
            if (!empty($criteria)) {
                $groupes = $this->getGroupeService()->search($criteria);
            } else {
                $groupes = $this->getGroupeService()->findAll();
            }
        }
        else {
                $groupes = $this->getGroupeService()->findAll();
        }
        return new ViewModel(['form' => $form, 'groupes' => $groupes]);
    }


    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAction()  : ViewModel
    {
        $groupe = $this->getGroupeFromRoute();
        return new ViewModel(['groupe' => $groupe]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherInfosAction(): ViewModel
    {
        $groupe = $this->getGroupeFromRoute();
        return new ViewModel(['groupe' => $groupe]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function ajouterAction(): ViewModel
    {
        $title = "Ajouter un groupe d'étudiants";
        $groupe = new Groupe();
        $annee = $this->getAnneeUniversitaireFromRoute();
        $groupe->setAnneeUniversitaire($annee);
        $form = $this->getAddGroupeForm();
        if (isset($annee)) {
            $form->setAnneeUniversitaire($annee);
        }
        $form->bind($groupe);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var Groupe $groupe */
                $groupe = $form->getData();
                try {
                    $groupe = $this->getGroupeService()->add($groupe);
                    $msg = sprintf("Le groupe %s a été créé.",
                        $groupe->getLibelle()
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
        /** @var Groupe $groupe */
        $groupe = $this->getGroupeFromRoute();
        $title = sprintf("Modifier le groupe %s", $groupe->getLibelle());

        $groupeService = $this->getGroupeService();

        $form = $this->getEditGroupeForm();
        $form->setAnneeUniversitaire($groupe->getAnneeUniversitaire());
        $form->bind($groupe);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var Groupe $groupe */
                    $groupe = $form->getData();
                    $groupeService->update($groupe);
                    $msg = sprintf("Le groupe %s a été modifé.", $groupe->getLibelle());
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'groupe' => $groupe]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function supprimerAction(): ViewModel
    {
        /** @var Groupe $groupe */
        $groupe = $this->getGroupeFromRoute();
        $title = sprintf("Supprimer le groupe %s", $groupe->getLibelle());

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment supprimer le groupe %s de l'année %s ?",
            $groupe->getLibelle(), $groupe->getAnneeUniversitaire()->getLibelle()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getGroupeService()->delete($groupe);
                $msg = sprintf("Le groupe %s a été supprimé.", $groupe->getLibelle());
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    /**
     * @return \Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerEtudiantsAction(): ViewModel
    {
        /** @var Groupe $groupe */
        $groupe = $this->getGroupeFromRoute();
        return new ViewModel(['groupe' => $groupe]);
    }

    /**
     * @return \Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerSessionsAction(): ViewModel
    {
        /** @var Groupe $groupe */
        $groupe = $this->getGroupeFromRoute();
        return new ViewModel(['groupe' => $groupe]);
    }

    /**
     * @return \Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function ajouterEtudiantsAction(): ViewModel
    {
        /** @var Groupe $groupe */
        $groupe = $this->getGroupeFromRoute();
        $title = sprintf("Ajouter des étudiants étudiants au groupe %s", $groupe->getLibelle());
        $validator = $this->getGroupeValidator();

        if ($etudiants = $this->getEtudiantsFromPost()) {
            try {
                $etudiants = array_filter($etudiants, function (Etudiant $etudiant) use ($groupe, $validator) {
                    return $validator->assertAjouter($groupe, $etudiant);
                });
                $this->getGroupeService()->addEtudiants($groupe, $etudiants);
                $msg = sprintf("%s étudiants ont été ajouté au groupe.", sizeof($etudiants));
                $this->sendSuccessMessage($msg, self::ACTION_AJOUTER_ETUDIANTS);
                $this->getObjectManager()->clear();
                $groupe = $this->getGroupeFromRoute();
            } catch (Exception $e) {
                $msg = sprintf("<strong>Une erreur est survenue</strong><div>%s</div>", $e->getMessage());
                $this->sendErrorMessage($msg, self::ACTION_AJOUTER_ETUDIANTS);
            }
        }

        $etudiants = $this->getEtudiantService()->findAll();
        $etudiants = array_filter($etudiants, function (Etudiant $etudiant) use ($groupe, $validator) {
            return $validator->assertAjouter($groupe, $etudiant);
        });

        return new ViewModel(['title' => $title, 'groupe' => $groupe, 'etudiants' => $etudiants]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function retirerEtudiantsAction() : ViewModel
    {
        /** @var Groupe $groupe */
        $groupe = $this->getGroupeFromRoute();
        $title = sprintf("Retirer des étudiants du groupe %s", $groupe->getLibelle());
        $validator = $this->getGroupeValidator();


        if ($etudiants = $this->getEtudiantsFromPost()) {
            try {
                $etudiants = array_filter($etudiants, function (Etudiant $etudiant) use ($groupe, $validator) {
                    return $validator->assertRetirer($groupe, $etudiant);
                });
                $this->getGroupeService()->removeEtudiants($groupe, $etudiants);
                $msg = sprintf("%s étudiants ont été retirés du groupe.", sizeof($etudiants));
                $this->sendSuccessMessage($msg, self::ACTION_RETIRER_ETUDIANTS);
                $groupe = $this->getGroupeFromRoute();
            } catch (Exception $e) {
                $msg = sprintf("<strong>Une erreur est survenue</strong><div>%s</div>", $e->getMessage());
                $this->sendErrorMessage($msg, self::ACTION_RETIRER_ETUDIANTS);
            }
        }

        $etudiants = $groupe->getEtudiants()->toArray();
        $etudiants = array_filter($etudiants, function (Etudiant $etudiant) use ($groupe, $validator) {
            return $validator->assertRetirer($groupe, $etudiant);
        });

        return new ViewModel(['title' => $title, 'groupe' => $groupe, 'etudiants' => $etudiants]);
    }
}