<?php


namespace Application\Controller\Parametre;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Form\Parametre\Traits\ParametreCoutTerrainFormAwareTrait;
use Application\Service\Parametre\Traits\ParametreCoutTerrainServiceAwareTrait;
use Exception;
use Laminas\View\Model\ViewModel;
/**
 * Class ParametresController
 * @package Application\Controller
 */
class ParametreCoutTerrainController extends AbstractActionController
{
    const ROUTE_INDEX = 'parametre/cout-terrain';
    const ROUTE_LISTER = 'parametre/cout-terrain/lister';
    const ROUTE_AJOUTER = "parametre/cout-terrain/ajouter";
    const ROUTE_MODIFIER = "parametre/cout-terrain/modifier";
    const ROUTE_SUPPRIMER = "parametre/cout-terrain/supprimer";

    const ACTION_INDEX = 'index';
    const ACTION_LISTER = 'lister';
    const ACTION_AJOUTER = 'ajouter';
    const ACTION_MODIFIER = 'modifier';
    const ACTION_SUPPRIMER = 'supprimer';

    const EVENT_AJOUTER= "event-ajouter-parametre-cout-terrain";
    const EVENT_MODIFIER= "event-modifier-parametre-cout-terrain";
    const EVENT_SUPPRIMER= "event-supprimer-parametre-cout-terrain";

    use ConfirmationFormAwareTrait;
    use ParametreCoutTerrainFormAwareTrait;
    use ParametreCoutTerrainServiceAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction(): ViewModel
    {
        $parametres = $this->getParametreCoutTerrainService()->findAll();
        return new ViewModel(['parametres' => $parametres]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $parametres = $this->getParametreCoutTerrainService()->findAll();
        return new ViewModel(['parametres' => $parametres]);
    }

    public function ajouterAction(): ViewModel
    {
        $title = "Ajouter un coût fixe à un terrain de stage";
        $form = $this->getAddParametreTerrainCoutAffectationFixeForm();
        $form->bind(new ParametreTerrainCoutAffectationFixe());
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ParametreTerrainCoutAffectationFixe $parametre */
                    $parametre = $form->getData();
                    $this->getParametreCoutTerrainService()->add($parametre);
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
        $title = "Modifier le cout fixe du terrain";
        /** @var ParametreTerrainCoutAffectationFixe $parametre */
        $parametre = $this->getParametreTerrainCoutAffectationFixeFromRoute();
        $form = $this->getEditParametreCoutTerrainForm();
        $form->bind($parametre);
        $form->fixerTerrainStage($parametre->getTerrainStage());

        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ParametreTerrainCoutAffectationFixe $parametre */
                    $parametre = $form->getData();
                    $this->getParametreCoutTerrainService()->update($parametre);
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
        $title = "Supprimer le coût fixe du terrain";

        /** @var ParametreTerrainCoutAffectationFixe $parametre */
        $parametre = $this->getParametreTerrainCoutAffectationFixeFromRoute();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vraiment supprimer le cout fixe pour le terrain de stage %s ?",
            $parametre->getTerrainStage()->getLibelle()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getParametreCoutTerrainService()->delete($parametre);
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