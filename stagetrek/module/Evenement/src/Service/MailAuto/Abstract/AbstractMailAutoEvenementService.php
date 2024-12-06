<?php

namespace Evenement\Service\MailAuto\Abstract;

use Application\Service\Parametre\ParametreService;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Evenement\Service\Evenement\Traits\EvenementEntitiesLinkerServiceAwareTrait;
use Exception;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UnicaenEvenement\Entity\Db\Etat;
use UnicaenEvenement\Entity\Db\Evenement;
use UnicaenEvenement\Service\Etat\EtatServiceAwareTrait;
use UnicaenEvenement\Service\Evenement\EvenementServiceAwareTrait;
use UnicaenEvenement\Service\EvenementCollection\EvenementCollectionServiceAwareTrait;
use UnicaenEvenement\Service\Type\TypeServiceAwareTrait;
use UnicaenMail\Service\Mail\MailServiceAwareTrait;

abstract class AbstractMailAutoEvenementService implements ObjectManagerAwareInterface
    , MailAutoEvenementServiceInterface
{
    use EtatServiceAwareTrait;
    use EvenementServiceAwareTrait;
    use EvenementCollectionServiceAwareTrait;
    use TypeServiceAwareTrait;
    use ProvidesObjectManager;
    use MailServiceAwareTrait;
    use EvenementEntitiesLinkerServiceAwareTrait;

    /**
     * @param Evenement $evenement
     * @return Evenement
     */
    public function ajouter(Evenement $evenement): Evenement
    {
        $this->evenementService->create($evenement);
        return $evenement;
    }

    /**
     * @param Evenement $evenement
     * @param Etat $etat
     * @return Evenement
     */
    public function changerEtat(Evenement $evenement, Etat $etat): Evenement
    {
        $evenement->setEtat($etat);
        $this->evenementService->update($evenement);

        return $evenement;
    }

    /**
     * @param Evenement $evenement
     * @return Evenement
     */
    public function supprimer(Evenement $evenement): Evenement
    {
        $this->evenementService->delete($evenement);
        return $evenement;
    }


    /** @var ServiceManager|null $serviceManager */
    protected ?ServiceManager $serviceManager = null;

    /** @return ServiceManager|null */
    public function getServiceManager() : ?ServiceManager
    {
        return $this->serviceManager;
    }

    /**
     * @param ServiceManager $serviceManager
     * @return AbstractMailAutoEvenementService
     */
    public function setServiceManager(ServiceManager $serviceManager) : static
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    //Fonctions de créations des événements en fonction du type de mail automatique
    /** @var array $eventsEtats */
    protected array $eventsEtats = [];
    protected function getEventEtat($code)
    {
        if(!isset($this->eventsEtats[$code])){
            $etat = $this->getEtatEvenementService()->findByCode($code);
            if($etat){
                $this->eventsEtats[$code] = $etat;
            }
        }
        return $this->eventsEtats[$code];
    }

    use ParametreServiceAwareTrait;

    /**
     * @throws \Exception
     */
    protected function getParametreService() : ?ParametreService
    {
        if($this->parametreService === null){
            try {
                $this->parametreService = $this->getServiceManager()->get(ParametreService::class);
            } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
                throw new Exception($e->getMessage());
            }
        }
        return $this->parametreService;
    }
}