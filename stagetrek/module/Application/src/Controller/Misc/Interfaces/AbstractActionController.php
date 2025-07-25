<?php

namespace Application\Controller\Misc\Interfaces;

use Application\Controller\Misc\Traits\ActionsMessagesToolsTrait;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Misc\RouterToolsTrait;
use Application\Misc\UserToolsTrait;
use Application\Misc\Util;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
//use Mail\Exception\MailException;

/**
 * abstract class AbstractActionController
 * @package Application\Controller\Interfaces
 * @author Thibaut Vallée <thibaut.vallée at unicaen.fr>
 * Classe abstraite permettant de gérer les fonctions commune aux multiples controlleur
 *
 * @method isAllowed($resource, $privilege = null)
 * @method FlashMessenger flashMessenger()
 */
abstract class AbstractActionController extends \Laminas\Mvc\Controller\AbstractActionController implements
    ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    use ConfirmationFormAwareTrait;
    use ActionsMessagesToolsTrait;
    use RouterToolsTrait;
    use UserToolsTrait;


    //Surcharge capturant les différents types d'erreur afin de mieux les gerer
    public function onDispatch(MvcEvent $e) : mixed
    {
        try{
            return parent::onDispatch($e);
        }
        //TODO : a revoir
//        catch (MailException $exception){
//            $vm =  $this->renderError("Mail non envoyé", $exception->getMessage());
//            $e->setResult($vm);
//            return $vm;
//        }
        catch (Exception $exception){
            $vm = $this->renderError("Une erreur est survenue", $exception->getMessage());
            $e->setResult($vm);
            return $vm;
        }
    }


    /**
     * @param $title
     * @param $text
     * @return ViewModel
     */
    protected function renderError($title = null, $text = null) : ViewModel
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/default/alert');
        $vm->setVariables([
            'title' => ($title) ?? "Une erreur est survenue",
            'text' => ($text) ?? "Une erreur est survenue lors de l'execution de l'action.",
            'alert' => 'alert-danger',
        ]);
        return $vm;
    }

    /**
     * @param $title
     * @param $text
     * @return ViewModel
     */
    protected function renderWarning($title = null, $text = null) :  ViewModel
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/default/alert');
        $vm->setVariables([
            'title' => ($title) ?? "Une erreur est survenue",
            'text' => ($text) ?? "Une erreur est survenue lors de l'execution de l'action.",
            'alert' => 'alert-warning',
        ]);
        return $vm;
    }

    /**
     * @param $title
     * @param $text
     * @return ViewModel
     */
    protected function renderSuccess($title = null, $text = null) :  ViewModel
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/default/alert');
        $vm->setVariables([
            'title' => ($title) ?? "Action terminée",
            'text' => ($text) ?? "L'action a été exécuté avec succés!",
            'alert' => 'alert-success',
        ]);
        return $vm;
    }

    /**
     * @param $title
     * @param $text
     * @param $action
     * @return ViewModel
     */
    protected function renderConfirmation($title = null, $text = null, $action = null) :  ViewModel
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/default/confirmation');
        $vm->setVariables([
            'title' => ($title) ?? "Confirmation",
            'text' => ($text) ?? "&Ecirc;tes  vous sûr" . Util::POINT_MEDIANT . "e de voulour effectuer cette action ?",
            'action' => $action,
        ]);
        return $vm;
    }
}