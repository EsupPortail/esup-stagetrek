<?php

namespace Application\Controller\Notification;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\MessageInfo;
use Application\Form\Notification\Traits\MessageInfoFormAwareTrait;
use Application\Service\Notification\Traits\MessageInfoServiceAwareTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;

/**
 * Class MessageInfoController
 * @package Application\Controller
 * @method FlashMessenger flashMessenger()
 */
class MessageInfoController extends AbstractActionController
{
    /** Accés aux entités */
    use MessageInfoServiceAwareTrait;
    use MessageInfoFormAwareTrait;

    /** ROUTES */
    const ROUTE_INDEX = "message";
    const ROUTE_LISTER = "message/lister";
    const ROUTE_AFFICHER = "message/afficher";
    const ROUTE_AJOUTER = "message/ajouter";
    const ROUTE_MODIFIER = "message/modifier";
    const ROUTE_SUPPRIMER = "message/supprimer";

    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AFFICHER = "afficher";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";

    const EVENT_AJOUTER = "event-ajouter-message-info";
    const EVENT_MODIFIER= "event-modifier-message-info";
    const EVENT_SUPPRIMER = "event-supprimer-message-info";

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction(): ViewModel
    {
        $messages = $this->getMessageInfoService()->findAll();
        return new ViewModel(['messages' => $messages]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $messages = $this->getMessageInfoService()->findAll();
        return new ViewModel(['messages' => $messages]);
    }

    public function ajouterAction(): ViewModel
    {
        $title = "Ajouter un message d'information";
        $messageInfo = new MessageInfo();
        $form = $this->getAddMessageInfoForm();
        $form->bind($messageInfo);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var MessageInfo $messageInfo */
                $messageInfo = $form->getData();
                try {
                    $this->getMessageInfoService()->add($messageInfo);
                    $msg ="Le message a été ajouté";
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' =>$title, 'form' => $form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAction() : ViewModel
    {
        $title = "Modifier le message d'information";
        $messageInfo = $this->getMessageInfoFromRoute();
        $form = $this->getEditMessageInfoForm();
        $form->bind($messageInfo);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var MessageInfo $messageInfo */
                $messageInfo = $form->getData();
                try {
                    $this->getMessageInfoService()->update($messageInfo);
                    $msg ="Le message a été modifié";
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return  new ViewModel(['title' =>  $title, 'form' => $form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function supprimerAction(): ViewModel
    {

        $title = "Supprimer la question";
        $messageInfo = $this->getMessageInfoFromRoute();
        $service = $this->getMessageInfoService();

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment supprimer le message d'information ?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $service->delete($messageInfo);
                $msg ="Le message a été supprimé";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);

            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }
}