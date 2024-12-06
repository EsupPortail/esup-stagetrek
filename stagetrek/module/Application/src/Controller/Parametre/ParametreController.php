<?php


namespace Application\Controller\Parametre;


use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\Parametre;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Form\Parametre\Traits\ParametreFormAwareTrait;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Exception;
use Laminas\View\Model\ViewModel;

/**
 * Class ParametresController
 * @package Application\Controller
 */
class ParametreController extends AbstractActionController
{ //TODO : permettre d'ajouter/modifier/supprimer des annuaires ? (sachant qu'a l'usage il faut du code derriére)

    const ROUTE_INDEX = 'parametre';
    const ROUTE_LISTER = 'parametre/lister';
    const ROUTE_MODIFIER = "parametre/modifier";

    const ACTION_INDEX = 'index';
    const ACTION_LISTER = 'lister';
    const ACTION_MODIFIER = 'modifier';

    const EVENT_MODIFIER= "event-modifier-parametre";

    use ConfirmationFormAwareTrait;
    use ParametreServiceAwareTrait;
    use ParametreFormAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction(): ViewModel
    {
        $parametres = $this->getParametreService()->findAll();
        $parametres = Parametre::sortParametres($parametres);
        return new ViewModel(['parametres' => $parametres]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $parametres = $this->getParametreService()->findAll();
        $parametres = Parametre::sortParametres($parametres);
        return new ViewModel(['parametres' => $parametres]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAction(): ViewModel
    {
        $title = "Modifier le paramètre";
        /** @var Parametre $parametre */
        $parametre = $this->getParametreFromRoute();
        $form = $this->getEditParametreForm();
        $form->bind($parametre);

        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var Parametre $parametre */
                    $parametre = $form->getData();
                    $this->getParametreService()->update($parametre);
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
}