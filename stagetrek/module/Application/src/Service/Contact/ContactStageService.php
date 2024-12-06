<?php

namespace Application\Service\Contact;

use Application\Entity\Db\ContactStage;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\Stage;
use Application\Entity\Db\ValidationStage;
use Application\Provider\Parametre\ParametreProvider;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\Service\Stage\Traits\ValidationStageServiceAwareTrait;
use DateInterval;
use DateTime;
use Exception;
use Ramsey\Uuid\Uuid;
use UnicaenMail\Service\Mail\MailServiceAwareTrait;

class ContactStageService extends CommonEntityService
{

    use ParametreServiceAwareTrait;
    use MailServiceAwareTrait;
    use ValidationStageServiceAwareTrait;


    public function getEntityClass(): string
    {
        return ContactStage::class;
    }

    //Trie par défaut par date décroissante
    public function findAll(): array
    {
        $result = $this->getObjectRepository()->findBy([], ['id' => 'asc']);
        return $this->getList($result);
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return ContactStage
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function add(mixed $entity, string $serviceEntityClass = null): ContactStage
    {
        /** @var ContactStage $contactStage */
        $contactStage = $entity;
        $contact = $contactStage->getContact();
        if(!isset($contact)){throw new Exception("Le contact n'as pas été correctement définie");}
        //On vérifie que le stage est "valide"
        $stage = $contactStage->getStage();
        if(!isset($stage)){throw new Exception("Le contact n'est pas associée à un stage");}
        /** @var ContactStage $cs */
        foreach ($contact->getContactsStages() as $cs){
            if($cs->getStage()->getId() == $stage->getId()){
                throw new Exception("Le contact est déjà associé au stage");
            }
        }
        $this->getObjectManager()->persist($contactStage);
        $this->getObjectManager()->flush($contactStage);
        $validationStage = $stage->getValidationStage();
        $this->getValidationStageService()->updateEtat($validationStage);
        return $contactStage;
    }

    /**
     * @throws \Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null)  : ContactStage
    {
        /** @var ContactStage $contactStage */
        $contactStage = $entity;
        $stage = $contactStage->getStage();
        if(!isset($stage)){throw new Exception("Le contact n'est pas associée à un stage");}
        $this->getObjectManager()->flush($contactStage);
        $validationStage = $stage->getValidationStage();
        $this->getValidationStageService()->updateEtat($validationStage);
        return $contactStage;
    }


    /**
     * TODO : script sql a transformer en Php
     * @throws Exception
     */
    public function updateContactsStage() : static
    {
        $this->execProcedure('update_contacts_stages');
        return $this;
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \DateMalformedIntervalStringException
     * @throws \Exception
     */
    public function genererTokenValidation(ContactStage $contact): ContactStage
    {
        $stage = $contact->getStage();
        $token = null;
        $cpt = 0;
        while ($token == null && $cpt++ <= 10) {
            $token = Uuid::uuid4()->toString();
            //Vérification qu'il n'est pas déjà utilisé (peu probable mais au cas ou
            $tokenExiste = $this->getObjectRepository()->findOneBy(['tokenValidation' => $token]);
            if (!$tokenExiste) {
                break;
            }
            $token = null;
        }
        if (!$token) {
            throw new Exception("Echec de la génération d'un lien de validation unique.");
        }

        $today = new DateTime();
        $dateFinToken = $today;
        if ($today < $stage->getDateFinValidation()) {
            $dateFinToken = $stage->getDateFinValidation();
        } else {
            $delay = $this->getParametreService()->getParametreValue(ParametreProvider::DUREE_TOKEN_VALDATION_STAGE);
            $dateFinToken->add(new DateInterval('P' . $delay . 'D'));
        }
        $dateFinToken->setTime(23,59);
        $contact->setTokenValidation($token);
        $contact->setTokenExpirationDate($dateFinToken);
        $this->getObjectManager()->flush($contact);
        return $contact;
    }


}