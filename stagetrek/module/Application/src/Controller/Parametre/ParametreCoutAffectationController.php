<?php


namespace Application\Controller\Parametre;


use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ParametreCoutAffectation;
use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Form\Parametre\Traits\ParametreCoutAffectationFormAwareTrait;
use Application\Service\Parametre\Traits\ParametreCoutAffectationServiceAwareTrait;
use Exception;
use Laminas\View\Model\ViewModel;

/**
 * Class ParametresController
 * @package Application\Controller
 */
class ParametreCoutAffectationController extends AbstractActionController
{
    const ROUTE_INDEX = 'parametre/cout-affectation';
    const ROUTE_LISTER = 'parametre/cout-affectation/lister';
    const ROUTE_AJOUTER = "parametre/cout-affectation/ajouter";
    const ROUTE_MODIFIER = "parametre/cout-affectation/modifier";
    const ROUTE_SUPPRIMER = "parametre/cout-affectation/supprimer";

    const ACTION_INDEX = 'index';
    const ACTION_LISTER = 'lister';
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = 'modifier';
    const ACTION_SUPPRIMER = 'supprimer';

    const EVENT_AJOUTER= "event-ajouter-parametre-cout-affectation";
    const EVENT_MODIFIER= "event-modifier-parametre-cout-affectation";
    const EVENT_SUPPRIMER= "event-supprimer-parametre-cout-affectation";

    use ConfirmationFormAwareTrait;
    use ParametreCoutAffectationFormAwareTrait;
    use ParametreCoutAffectationServiceAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction(): ViewModel
    {
        $parametres = $this->getParametreCoutAffectationService()->findAll();
        return new ViewModel(['parametres' => $parametres]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $parametres = $this->getParametreCoutAffectationService()->findAll();
        return new ViewModel(['parametres' => $parametres]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function ajouterAction(): ViewModel
    {
        $title = "Ajouter un coût d'affectation";
        $form = $this->getAddParametreCoutAffectationForm();
        $parametre = new ParametreCoutAffectation();
        $rang = $this->getParametreCoutAffectationService()->getNextRang();
        $parametre->setRang($rang);
        $form->bind($parametre);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ParametreTerrainCoutAffectationFixe $parametre */
                    $parametre = $form->getData();
                    $this->getParametreCoutAffectationService()->add($parametre);
                    $msg = "Le paramètre a été créé";
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
        $title = "Modifier le cout des affectations";
        /** @var ParametreCoutAffectation $parametre */
        $parametre = $this->getParametreCoutAffectationFromRoute();
        $form = $this->getEditParametreCoutAffectationForm();
        $form->bind($parametre);

        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ParametreCoutAffectation $parametre */
                    $parametre = $form->getData();
                    $this->getParametreCoutAffectationService()->update($parametre);
                    $msg = "Le paramètre a été modifiée";
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
        $title = "Supprimer le coût de l'affectation";

        /** @var ParametreCoutAffectation $parametre */
        $parametre = $this->getParametreCoutAffectationFromRoute();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vraiment supprimer le cout des affectations de rang %s ?",
            $parametre->getRang()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getParametreCoutAffectationService()->delete($parametre);
                $msg = "Le paramètre a été supprimé.";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }
}