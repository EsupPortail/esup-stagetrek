<?php

namespace Application\Controller\Notification;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\FaqCategorieQuestion;
use Application\Form\Notification\Traits\FaqFormAwareTrait;
use Application\Service\Notification\Traits\FAQCategorieQuestionServiceAwareTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;

/**
 * Class FaqCategorieController
 * @method FlashMessenger flashMessenger()
 */
class FaqCategorieController extends AbstractActionController
{    /** Accés aux formulaires */
    /** ROUTES */
    const ROUTE_INDEX = "faq/categorie";
    const ROUTE_LISTER = "faq/categorie/lister";
    const ROUTE_AJOUTER = "faq/categorie/ajouter";
    const ROUTE_MODIFIER = "faq/categorie/modifier";
    const ROUTE_SUPPRIMER = "faq/categorie/supprimer";

    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";

    const EVENT_AJOUTER = "event-ajouter-faq-categorie";
    const EVENT_MODIFIER = "event-modifier-faq-categorie";
    const EVENT_SUPPRIMER = "event-supprimer-faq-categorie";


    use FAQCategorieQuestionServiceAwareTrait;
    use FaqFormAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction() : ViewModel
    {
        $categories = $this->getFaqCategorieQuestionService()->findAll();
        return new ViewModel(['categories' => $categories]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction() : ViewModel
    {
        $categories = $this->getFaqCategorieQuestionService()->findAll();
        return new ViewModel(['categories' => $categories]);
    }

    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter une catégorie de question";
        $categorie = new FaqCategorieQuestion();
        $form = $this->getAddFaqCategorieQuestionForm();
        $form->bind($categorie);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var FaqCategorieQuestion $categorie */
                $categorie = $form->getData();
                try {
                    $this->getFaqCategorieQuestionService()->add($categorie);
                    $msg = "La catégorie a été ajoutée";
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
    public function modifierAction() : ViewModel
    {
        $title= "Modifier la catégorie de question";
        $categorie = $this->getFaqCategorieQuestionFromRoute();

        $form = $this->getEditFaqCategorieQuestionForm();
        $form->bind($categorie);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var FaqCategorieQuestion $categorie */
                $categorie = $form->getData();
                try {
                    $this->getFaqCategorieQuestionService()->update($categorie);
                    $msg = "La catégorie a été modifiée";
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                }  catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function supprimerAction() : ViewModel
    {
        $title = "Supprimer la catégorie de question";

        $categorie = $this->getFaqCategorieQuestionFromRoute();
        $service = $this->getFaqCategorieQuestionService();

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment supprimer la catégorie de question ?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $service->delete($categorie);
                $msg ="La catégorie de question a été supprimée";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);

            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

}