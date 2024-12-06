<?php
namespace Evenement\Service\Evenement\Traits;

use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use DoctrineModule\Persistence\ProvidesObjectManager;
use UnicaenEvenement\Entity\Db\Evenement;

/** @desc Trait permettant de fournir un lien entre les événements et certaines application spécifique */
trait EvenementEntitiesLinkerServiceAwareTrait
{
    use ProvidesObjectManager;

    /**
     * @param Evenement $evenement
     * @return Stage|null
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function getStageForEvenement(Evenement $evenement) : ?Stage
    {
        $sql = "SELECT stage_id FROM v_evenement_entities_linker where evenement_id = :evenement_id";
        $result = $this->getObjectManager()->getConnection()->executeQuery($sql, ['evenement_id' => $evenement->getId()]);
        $result = $result->fetchAllAssociative();
        //On regarde si l'événement existe déjà pour ne pas le regénérer.
        if(empty($result)){return null;}
        $stageId = (current($result)['stage_id']) ?? 0;
        /** @var Stage $stage */
        $stage = $this->getObjectManager()->getRepository(Stage::class)->find($stageId);
        return $stage;
    }

    /**
     * @param Evenement $evenement
     * @return SessionStage|null
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function getSessionForEvenement(Evenement $evenement) : ?SessionStage
    {
        $sql = "SELECT session_id FROM v_evenement_entities_linker where evenement_id = :evenement_id";
        $result = $this->getObjectManager()->getConnection()->executeQuery($sql, ['evenement_id' => $evenement->getId()]);
        $result = $result->fetchAllAssociative();
        //On regarde si l'événement existe déjà pour ne pas le regénérer.
        if(empty($result)){return null;}
        $sessionId = (current($result)['session_id']) ?? 0;
        /** @var SessionStage $session */
        $session = $this->getObjectManager()->getRepository(SessionStage::class)->find($sessionId);
        return $session;
    }

    /**
     * @param Evenement $evenement
     * @return Etudiant|null
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function getEtudiantForEvenement(Evenement $evenement) : ?Etudiant
    {
        $sql = "SELECT etudiant_id FROM v_evenement_entities_linker where evenement_id = :evenement_id";
        $result = $this->getObjectManager()->getConnection()->executeQuery($sql, ['evenement_id' => $evenement->getId()]);
        $result = $result->fetchAllAssociative();
        //On regarde si l'événement existe déjà pour ne pas le regénérer.
        if(empty($result)){return null;}
        $etudiantId = (current($result)['etudiant_id']) ?? 0;
        /** @var Etudiant $etudiant */
        $etudiant = $this->getObjectManager()->getRepository(Etudiant::class)->find($etudiantId);
        return $etudiant;
    }

    /**
     * @param Evenement $evenement
     * @return ContactStage|null
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function getContactStageForEvenement(Evenement $evenement) : ?ContactStage
    {
        $sql = "SELECT contact_stage_id FROM v_evenement_entities_linker where evenement_id = :evenement_id";
        $result = $this->getObjectManager()->getConnection()->executeQuery($sql, ['evenement_id' => $evenement->getId()]);
        $result = $result->fetchAllAssociative();
        //On regarde si l'événement existe déjà pour ne pas le regénérer.
        if(empty($result)){return null;}
        $contactStageId = (current($result)['contact_stage_id']) ?? 0;
        /** @var ContactStage $contact */
        $contact = $this->getObjectManager()->getRepository(ContactStage::class)->find($contactStageId);
        return $contact;
    }

    /**
     * Retourne l'ensemble des événements tel que
     * @param array $parametres
     * @return Evenement[]
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findEvenementsWithParametres(array $parametres) : array
    {
        $sql = "SELECT evenement_id FROM v_evenement_entities_linker where evenement_id is not null";
        $params=[];

        foreach ($parametres as $key => $param){
            switch ($key){
//                Pour rajouter des cotes autour du paramètres
                case 'type_code':
                case 'etat_code':
                    $sql .= sprintf(" and %s=:%s", $key, $key);
                break;
                default :
                    $sql .= sprintf(" and %s=:%s", $key, $key);

            }
            $params[':'.$key] = $param;
        }
        $stmt = $this->getObjectManager()->getConnection()->prepare($sql);
        $result = $stmt->executeQuery($params)->fetchAllAssociative();
        $evenements = [];
        foreach ($result as $res){
            $eventId = ($res['evenement_id']) ?? 0;
            $event = $this->getObjectManager()->getRepository(Evenement::class)->find($eventId);
            if($event){$evenements[$event->getId()] = $event;}
        }
        return $evenements;
    }


}