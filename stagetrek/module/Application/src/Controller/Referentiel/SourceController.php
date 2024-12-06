<?php

namespace Application\Controller\Referentiel;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\Source;
use Application\Form\Referentiel\SourceForm;
use Application\Form\Referentiel\Traits\SourceFormsAwareTrait;
use Application\Service\Referentiel\Traits\SourceServiceAwareTrait;
use Exception;
use Laminas\View\Model\ViewModel;

/**
 * Class SourceController
 */
class SourceController extends AbstractActionController
{

    const ROUTE_INDEX = 'referentiel/source';
    const ROUTE_LISTER = 'referentiel/source/lister';
    const ROUTE_AJOUTER = 'referentiel/source/ajouter';
    const ROUTE_MODIFIER = 'referentiel/source/modifier';
    const ROUTE_SUPPRIMER = 'referentiel/source/supprimer';

    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";

    const EVENT_AJOUTER = 'event-ajouter-source';
    const EVENT_MODIFIER = 'event-modifier-source';
    const EVENT_SUPPRIMER = 'event-supprimer-source';

    use SourceServiceAwareTrait;
    use SourceFormsAwareTrait;

    /**
     * @return \Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction(): ViewModel
    {
        $sources = $this->getSourceService()->findAll();
        return new ViewModel(['sources' => $sources]);
    }

    /**
     * @return \Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $sources = $this->getSourceService()->findAll();
        return new ViewModel(['sources' => $sources]);
    }

    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter une source de données";
        $form = $this->getAddSourceForm();
        $form->bind(new Source());
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var Source $source */
                    $source = $form->getData();
                    $this->getSourceService()->add($source);
                    $msg = "La source données a été ajouté";
                    $this->sendSuccessMessage($msg);
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
        $title = "Modifier la source de données";
        /** @var Source $source */
        $source = $this->getSourceFromRoute();

        $service = $this->getSourceService();
        $form = $this->getSourceEditForm();
        $form->bind($source);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var Source $source */
                    $source = $form->getData();
                    $service->update($source);
                    $msg = "La source a été modifiée";
                    $this->sendSuccessMessage($msg);
                    $form->bind($source);
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
        $title = "Supprimer la source de données";
        /** @var Source $source */
        $source = $this->getSourceFromRoute();

        $service = $this->getSourceService();

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment supprimer la source de données?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $service->delete($source);
                $msg = "La source a été supprimé";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

}