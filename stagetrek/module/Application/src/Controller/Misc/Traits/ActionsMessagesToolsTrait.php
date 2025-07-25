<?php

namespace Application\Controller\Misc\Traits;

use Application\Provider\Roles\RolesProvider;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;
use UnicaenUtilisateur\Entity\Db\Role;

/**
 * Traits permettant de réorienté vers des page générique de succés ou d'échec avec différentes informations
 *
 * @method isAllowed($resource, $privilege = null)
 * @method FlashMessenger flashMessenger()
 */
trait ActionsMessagesToolsTrait
{

    /************
     * Envoies des messages
     ************/

    protected function sendSuccessMessage($msg, $namespace = null): void
    {
        if ($msg == null || $msg == "") {
            return;
        }
        if ($namespace == null) {
            $this->flashMessenger()->addSuccessMessage($msg);
        } else {
            $this->flashMessenger()->addMessage($msg, $namespace . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::SUCCESS);
        }
    }

    protected function sendInfoMessage($msg, $namespace = null): void
    {
        if ($msg == null || $msg == "") {
            return;
        }
        if ($namespace == null) {
            $this->flashMessenger()->addInfoMessage($msg);
        } else {
            $this->flashMessenger()->addMessage($msg, $namespace . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::INFO);
        }
    }

    protected function sendWarningMessage($msg, $namespace = null): void
    {
        if ($msg == null || $msg == "") {
            return;
        }
        if ($namespace == null) {
            $this->flashMessenger()->addWarningMessage($msg);
        } else {
            $this->flashMessenger()->addMessage($msg, $namespace . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::WARNING);
        }
    }

    protected function sendErrorMessage($msg, $namespace = null): void
    {
        if ($msg == null || $msg == "") {
            return;
        }
        if ($namespace == null) {
            $this->flashMessenger()->addErrorMessage($msg);
        } else {
            $this->flashMessenger()->addMessage($msg, $namespace . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::ERROR);
        }
    }

    //Templates de messages "générique"
    protected function sendAddSuccessMessage($msg = "Ajout effectué")
    {
        $this->flashMessenger()->addSuccessMessage($msg);
        return $msg;
    }

    protected function sendAddWarningMessage($msg = "Ajout effectué mais avec une erreur non critique")
    {
        $this->flashMessenger()->addWarningMessage($msg);
        return $msg;
    }

    protected function sendEditSuccessMessage($msg = "Modification effectuée")
    {
        $this->flashMessenger()->addSuccessMessage($msg);
        return $msg;
    }

    //Succés mais avec un message d'alerte
    protected function sendEditWarningMessage($msg = "Modification effectuée mais avec une erreur non critique")
    {
        $this->flashMessenger()->addWarningMessage($msg);
        return $msg;
    }

    protected function sendEditInfoMessage($msg = "Modification effectuée")
    {
        $this->flashMessenger()->addInfoMessage($msg);
        return $msg;
    }

    protected function sendDeleteSuccessMessage($msg = "Suppression effectuée")
    {
        $this->flashMessenger()->addSuccessMessage($msg);
        return $msg;
    }


    protected function sendImportSuccessMessage($msg = "Import effectué")
    {
        $this->flashMessenger()->addSuccessMessage($msg);
        return $msg;
    }

    /**
     * @param string|null $title
     * @param string|null $msg
     * @param string|null $subtext
     * @param string $priority //Pour modifier éventuellement la priorité du message affiché
     * @return \Laminas\View\Model\ViewModel
     */
    protected function successAction(string $title = null, string $msg = null, string $subtext = null, string $priority = Messenger::SUCCESS): ViewModel
    { //TODO : a revoir l'usage
        if (!isset($msg)) {
            $msg = "L'action a été effectuée";
        }
        if ($title === null) {
            $title = "Action terminée";
        }
        $vm = new ViewModel();
        $vm->setVariable('title', $title);
        $vm->setVariable('message', $msg);
        $vm->setVariable('subtext', $subtext);
        $vm->setVariable('priority', $priority);
        $vm->setTemplate('layout/templates/success-template');
        return $vm;
    }


    /**
     * Si l'action a échouée affichage d'un messge dans un template
     * @param null $title
     * @param null $msg
     * @param \Exception|null $exception
     * @param null $subtext
     * @param string $priority
     * @return \Laminas\View\Model\ViewModel
     */
    protected function failureAction($title = null, $msg = null, Exception $exception = null, $subtext = null, string $priority=Messenger::ERROR): ViewModel
    { /** TODO : a remplacer par renderError */
        if($priority==Messenger::ERROR){$priority='danger';}//Pour avoir les bon type de template
        if (!isset($msg) || $msg == "") {
            $msg = "Une erreur est survenue.";
        }
        if (isset($exception) && $this->canSeeException()) {
            $msg .= sprintf("<br/> <strong>Exception :</strong> %s", $exception->getMessage());
//            $routes = explode('#', $exception->getTraceAsString());
//            $msg .= "<ul>";
//            foreach ($routes as $route) {
//                if ($route != "")
//                    $msg .= sprintf("<li>%s</li>", $route);
//            }
//            $msg .= "</ul>";
        }
        if ($title === null) {
            $title = "Echec de l'action";
        }

        $vm = new ViewModel();
        $vm->setVariable('title', $title);
        $vm->setVariable('message', $msg);
        $vm->setVariable('subtext', $subtext);
        $vm->setVariable('priority', $priority);
        $vm->setTemplate('layout/templates/failure-template');
        return $vm;
    }

    /**
     * Permet d'afficher les exceptions uniquement si l'on en as le droit
     * TODO : remplacer le fait que l'on ais le role "Administrateur" par un privilége
     * @return boolean
     * A priori si l'on n'a pas de "user" on aura d'autres erreur avant
     */
    protected function canSeeException(): bool
    { //TODO : a revoir car déjà présent dans la conf
        $user = $this->getUser();
        if (!$user) {
            return false;
        }
        /** @var Role $role */
        foreach ($this->getUser()->getRoles() as $role) {
            if ($role->getRoleId() == RolesProvider::ADMIN_TECH) {
                return true;
            }
        }
        return false;
    }
}