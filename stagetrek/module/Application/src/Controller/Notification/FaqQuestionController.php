<?php

namespace Application\Controller\Notification;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\Faq;
use Application\Form\Notification\Traits\FaqFormAwareTrait;
use Application\Service\Notification\Traits\FaqServiceAwareTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\View\Model\ViewModel;
use UnicaenAuthentification\Service\UserContext;

/**
 * Class FaqQuestionController
 * @method FlashMessenger flashMessenger()
 */
class FaqQuestionController extends AbstractActionController
{
    const ROUTE_INDEX = "faq";
    const ROUTE_LISTER = "faq/question/lister";
    const ROUTE_AJOUTER = "faq/question/ajouter";
    const ROUTE_MODIFIER = "faq/question/modifier";
    const ROUTE_SUPPRIMER = "faq/question/supprimer";
    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";

    const EVENT_AJOUTER = "event-ajouter-faq-question";
    const EVENT_MODIFIER = "event-modifier-faq-question";
    const EVENT_SUPPRIMER = "event-supprimer-faq-question";

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction(): ViewModel
    {
        $role = $this->getCurrentUserRole();
        $questions = $this->getFaqQuestionService()->findAllForRole($role);
        return new ViewModel(['questions' => $questions]);

    }


    use FaqServiceAwareTrait;
    use FaqFormAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $role = $this->getCurrentUserRole();
        $questions = $this->getFaqQuestionService()->findAllForRole($role);
        return new ViewModel(['questions' => $questions]);
    }

    public function ajouterAction(): ViewModel
    {
        $title = "Ajouter une question";
        $question = new FAQ();
        $form = $this->getAddFaqQuestionForm();
        $form->bind($question);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var FAQ $question */
                $question = $form->getData();
                try {
                    $this->getFaqQuestionService()->add($question);
                    $msg = "La question a été ajoutée";
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
        $title = "Modifier la question / réponse";
        $question = $this->getFaqQuestionFromRoute();
        $form = $this->getEditFaqQuestionForm();
        $form->bind($question);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var Faq $faq */
                $question = $form->getData();
                try {
                    $this->getFaqQuestionService()->update($question);
                    $msg = "La question a été modifiée";
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                }  catch (Exception $e) {
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

        $title = "Supprimer la question";
        $faq = $this->getFaqQuestionFromRoute();
        $service = $this->getFaqQuestionService();

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment supprimer la question ?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $service->delete($faq);
                $msg ="La question a été supprimée";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);

            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }


    protected UserContext $serviceUserContext;

    /**
     * @return UserContext
     */
    public function getServiceUserContext() : UserContext
    {
        return $this->serviceUserContext;
    }

    /**
     * @param UserContext $serviceUserContext
     */
    public function setServiceUserContext( UserContext$serviceUserContext): void
    {
        $this->serviceUserContext = $serviceUserContext;
    }

    /** @return RoleInterface|null */
    private function getCurrentUserRole(): ?RoleInterface
    {
        return $this->getServiceUserContext()->getSelectedIdentityRole();
    }
}