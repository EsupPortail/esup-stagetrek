<?php

namespace Application\Controller\Index;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Service\Notification\Traits\MessageInfoServiceAwareTrait;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use UnicaenUtilisateur\Entity\Db\User;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * Class IndexController
 * @package Application\Controller\Index
 *
 * @method FlashMessenger flashMessenger()
 * @method ZfcUserAuthentication zfcUserAuthentication()
 */
class IndexController extends AbstractActionController
{
    const ROUTE_INDEX = "home";
    const ACTION_INDEX = 'index';


    use MessageInfoServiceAwareTrait;
    //Envoie d'un flash messages si l'étudiant doit faire une action

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction(): ViewModel
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/index/index'); //Pour éviter de chercher la pages index/index/index ...
        /** @var User $utilisateur */
        $utilisateur = null;
        if ($this->zfcUserAuthentication()->getAuthService()->hasIdentity()) {
            $utilisateur = $this->zfcUserAuthentication()->getAuthService()->getIdentity()['db'];
        }
        if (!$utilisateur) {
            $this->zfcUserAuthentication()->getAuthService()->clearIdentity();
        }
        $messages = $this->getMessageInfoService()->getMessagesForCurrentUser();
        $vm->setVariable("utilisateur", $utilisateur);
        $vm->setVariable("messages", $messages);
        return $vm;
    }

    public function flashMessageAction(): ViewModel
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/index/flash-message');
        return $vm;

    }
}