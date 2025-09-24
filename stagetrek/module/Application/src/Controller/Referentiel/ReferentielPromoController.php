<?php


namespace Application\Controller\Referentiel;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Traits\Referentiel\HasReferentielPromoTrait;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Form\Referentiel\Traits\ReferentielPromoFormAwareTrait;
use Application\Service\Referentiel\Traits\ReferentielPromoServiceAwareTrait;
use Exception;
use Laminas\View\Model\ViewModel;

/**
 * Class ReferentielPromoController
 * @package Application\Controller
 */
class ReferentielPromoController extends AbstractActionController
{
    /** ROUTES */
    const ROUTE_INDEX = 'referentiel/promo';
    const ROUTE_LISTER = 'referentiel/promo/lister';
    const ROUTE_AJOUTER = 'referentiel/promo/ajouter';
    const ROUTE_MODIFIER = 'referentiel/promo/modifier';
    const ROUTE_SUPPRIMER = 'referentiel/promo/supprimer';

    /** ACTIONS */
    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";

    const EVENT_AJOUTER = 'event-ajouter-referentiel-promo';
    const EVENT_MODIFIER = 'event-modifier-referentiel-promo';
    const EVENT_SUPPRIMER = 'event-supprimer-referentiel-promo';

    use ReferentielPromoServiceAwareTrait;
    use ConfirmationFormAwareTrait;
    use ReferentielPromoFormAwareTrait;

    use HasReferentielPromoTrait;

    /**
     * @return \Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction(): ViewModel
    {
        $referentielsPromos = $this->getReferentielPromoService()->findAll();
        return new ViewModel(['referentielsPromos' => $referentielsPromos]);
    }

    /**
     * @return \Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $referentielsPromos = $this->getReferentielPromoService()->findAll();
        return new ViewModel(['referentielsPromos' => $referentielsPromos]);
    }

    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter un référentiel de promotion étudiantes";
        $form = $this->getAddReferentielPromoForm();
        $form->bind(new ReferentielPromo());
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ReferentielPromo $referentielPromo */
                    $referentielPromo = $form->getData();
                    $this->getReferentielPromoService()->add($referentielPromo);
                    $msg = "Le référentiel de promotion a été ajouté";
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
        $title = "Modifier le référentiel de promotion étudiantes";
        /** @var ReferentielPromo $referentiel */
        $referentiel = $this->getReferentielPromoFromRoute();

        $service = $this->getReferentielPromoService();
        $form = $this->getEditReferentielPromoForm();
        $form->bind($referentiel);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ReferentielPromo $referentiel */
                    $referentiel = $form->getData();
                    $service->update($referentiel);
                    $msg = "Le référentiel a été modifié";
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
        $title = "Supprimer le référentiel de promotion étudiantes";
        /** @var ReferentielPromo $referentiel */
        $referentiel = $this->getReferentielPromoFromRoute();
        $service = $this->getReferentielPromoService();

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment supprimer le référentiel de promo ?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $service->delete($referentiel);
                $msg = "Le référentiel a été supprimé";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }
}