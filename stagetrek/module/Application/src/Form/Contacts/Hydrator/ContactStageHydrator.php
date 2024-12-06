<?php


namespace Application\Form\Contacts\Hydrator;

use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Form\Contacts\Fieldset\ContactStageFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

class ContactStageHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var ContactStage $contactStage */
        $contactStage = $object;
        $contact = $contactStage->getContact();
        $stage = $contactStage->getStage();
        $etudiant = (isset($stage)) ? ($stage->getEtudiant()) : null;
        $session = (isset($stage)) ? ($stage->getSessionStage()) : null;

        $data = [];
        $data[ContactStageFieldset::CONTACT] = (isset($contact)) ? $contact->getId() : null;
        $data[ContactStageFieldset::ETUDIANT_ID] = (isset($etudiant)) ? $etudiant->getId() : null;
        $data[ContactStageFieldset::ETUDIANT] = (isset($etudiant)) ? $etudiant->getDisplayName() : null;
        $data[ContactStageFieldset::SESSION] = (isset($session)) ? $session->getId() : null;
        $data[ContactStageFieldset::STAGE_ID] = (isset($stage)) ? $stage->getId() : null;

        $data[ContactStageFieldset::IS_VISIBLE_ETUDIANT] = $contactStage->isVisibleParEtudiant();
        $data[ContactStageFieldset::IS_RESPONSABLE_STAGE] = $contactStage->isResponsableStage();
        $data[ContactStageFieldset::CAN_VALIDER_STAGE] = $contactStage->canValiderStage();
        $data[ContactStageFieldset::SEND_MAIL_AUTO_VALIDATION_STAGE] = $contactStage->sendMailAutoValidationStage();
        $data[ContactStageFieldset::SEND_MAIL_AUTO_RAPPEL_VALIDATION_STAGE] = $contactStage->sendMailAutoRappelValidationStage();

        $data[ContactStageFieldset::IS_SIGNATAIRE_CONVENTION] = $contactStage->isSignataireConvention();
        if($contactStage->isSignataireConvention()){
            $data[ContactStageFieldset::PRIORITE_ORDRE_SIGNATURE] = $contactStage->getPrioriteOrdreSignature();
        }
        else{
            $data[ContactStageFieldset::PRIORITE_ORDRE_SIGNATURE] = null;
        }

        return $data;
    }

    /**
     * @param array $data
     * @param $object
     * @return object
     */
    public function hydrate(array $data, $object): object
    {
        /** @var ContactStage $contactStage */
        $contactStage = $object;
        $contactId = $data[ContactStageFieldset::CONTACT] ?? 0;
        $contact = $this->getObjectManager()->getRepository(Contact::class)->find($contactId);

        $stageId = isset($data[ContactStageFieldset::STAGE_ID]) ? intval($data[ContactStageFieldset::STAGE_ID]) : false;
        $stage = $this->getObjectManager()->getRepository(Stage::class)->find($stageId);


        if(!isset($stage)){
            $etudiantId = ($data[ContactStageFieldset::ETUDIANT_ID]) ?? 0;
            /** @var Etudiant $etudiant */
            $etudiant = $this->getObjectManager()->getRepository(Etudiant::class)->find($etudiantId);
            $sessionId = ($data[ContactStageFieldset::SESSION] )?? 0;
            $session = $this->getObjectManager()->getRepository(SessionStage::class)->find($sessionId);
            if(isset($session) && isset($etudiant)){
                $stage = $etudiant->getStageFor($session);
            }
        }



        $visible =  isset($data[ContactStageFieldset::IS_VISIBLE_ETUDIANT]) ? boolval($data[ContactStageFieldset::IS_VISIBLE_ETUDIANT]) : false;
        $responsable =  isset($data[ContactStageFieldset::IS_RESPONSABLE_STAGE]) ? boolval($data[ContactStageFieldset::IS_RESPONSABLE_STAGE]) : false;
        $canValidate =  isset($data[ContactStageFieldset::CAN_VALIDER_STAGE]) ? boolval($data[ContactStageFieldset::CAN_VALIDER_STAGE]) : false;
        $mailValidation =  isset($data[ContactStageFieldset::SEND_MAIL_AUTO_VALIDATION_STAGE]) ? boolval($data[ContactStageFieldset::SEND_MAIL_AUTO_VALIDATION_STAGE]) : false;
        $mailRappelValidation =  isset($data[ContactStageFieldset::SEND_MAIL_AUTO_RAPPEL_VALIDATION_STAGE]) ? boolval($data[ContactStageFieldset::SEND_MAIL_AUTO_RAPPEL_VALIDATION_STAGE]) : false;

        $isSingataire =  isset($data[ContactStageFieldset::IS_SIGNATAIRE_CONVENTION]) ? boolval($data[ContactStageFieldset::IS_SIGNATAIRE_CONVENTION]) : false;
        $priorite = null;
        if($isSingataire){
            $priorite = isset($data[ContactStageFieldset::PRIORITE_ORDRE_SIGNATURE]) ? intval($data[ContactStageFieldset::PRIORITE_ORDRE_SIGNATURE]) : null;
        }
        if($priorite<=0){
            $priorite=null;
        }

        $contactStage->setContact($contact);
        $contactStage->setStage($stage);
        $contactStage->setVisibleParEtudiant($visible);
        $contactStage->setIsResponsableStage($responsable);
        $contactStage->setCanValiderStage($canValidate);
        $contactStage->setSendMailAutoValidationStage($mailValidation);
        $contactStage->setSendMailAutoRappelValidationStage($mailRappelValidation);

        $contactStage->setIsSignataireConvention($isSingataire);
        $contactStage->setPrioriteOrdreSignature($priorite);
        $contactStage->setSendMailAutoListeEtudiantsStage(false);
        return $contactStage;
    }
}