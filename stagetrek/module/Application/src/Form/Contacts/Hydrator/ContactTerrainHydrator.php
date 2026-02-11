<?php


namespace Application\Form\Contacts\Hydrator;

use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\TerrainStage;
use Application\Form\Contacts\Fieldset\ContactTerrainFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

class ContactTerrainHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var ContactTerrain $contactTerrain */
        $contactTerrain = $object;
        $data = [];
        $data[ContactTerrainFieldset::CONTACT] = ($contactTerrain->getContact() !== null) ? $contactTerrain->getContact()->getId() : null;
        $data[ContactTerrainFieldset::TERRAIN] = ($contactTerrain->getTerrainStage() !== null) ? $contactTerrain->getTerrainStage()->getId() : null;
        $data[ContactTerrainFieldset::IS_VISIBLE_ETUDIANT] = $contactTerrain->isVisibleParEtudiant();
        $data[ContactTerrainFieldset::IS_RESPONSABLE_STAGE] = $contactTerrain->isResponsableStage();
        $data[ContactTerrainFieldset::CAN_VALIDER_STAGE] = $contactTerrain->canValiderStage();
        $data[ContactTerrainFieldset::SEND_MAIL_AUTO_VALIDATION_STAGE] = $contactTerrain->sendMailAutoValidationStage();
        $data[ContactTerrainFieldset::SEND_MAIL_AUTO_RAPPEL_VALIDATION_STAGE] = $contactTerrain->sendMailAutoRappelValidationStage();
        $data[ContactTerrainFieldset::SEND_MAIL_AUTO_LISTE_ETUDIANT_STAGE] = $contactTerrain->sendMailAutoListeEtudiantsStage();
        $data[ContactTerrainFieldset::IS_SIGNATAIRE_CONVENTION] = $contactTerrain->isSignataireConvention();
        if($contactTerrain->isSignataireConvention()){
            $data[ContactTerrainFieldset::PRIORITE_ORDRE_SIGNATURE] = $contactTerrain->getPrioriteOrdreSignature();
        }
        else{
            $data[ContactTerrainFieldset::PRIORITE_ORDRE_SIGNATURE] = null;
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
        /** @var ContactTerrain $contactTerrain */
        $contactTerrain = $object;
        $contactId = $data[ContactTerrainFieldset::CONTACT] ?? 0;
        $terrainId = $data[ContactTerrainFieldset::TERRAIN] ?? 0;
        $contact = $this->getObjectManager()->getRepository(Contact::class)->find($contactId);
        $terrain = $this->getObjectManager()->getRepository(TerrainStage::class)->find($terrainId);
        $visible =  isset($data[ContactTerrainFieldset::IS_VISIBLE_ETUDIANT]) ? boolval($data[ContactTerrainFieldset::IS_VISIBLE_ETUDIANT]) : false;
        $responsable =  isset($data[ContactTerrainFieldset::IS_RESPONSABLE_STAGE]) ? boolval($data[ContactTerrainFieldset::IS_RESPONSABLE_STAGE]) : false;
        $canValidate =  isset($data[ContactTerrainFieldset::CAN_VALIDER_STAGE]) ? boolval($data[ContactTerrainFieldset::CAN_VALIDER_STAGE]) : false;

        $isSingataire =  isset($data[ContactTerrainFieldset::IS_SIGNATAIRE_CONVENTION]) ? boolval($data[ContactTerrainFieldset::IS_SIGNATAIRE_CONVENTION]) : false;
        $priorite = null;
        if($isSingataire){
            $priorite = isset($data[ContactTerrainFieldset::PRIORITE_ORDRE_SIGNATURE]) ? intval($data[ContactTerrainFieldset::PRIORITE_ORDRE_SIGNATURE]) : null;
        }
        if($priorite<=0){
            $priorite=null;
        }

        $mailListeEtudiants =  isset($data[ContactTerrainFieldset::SEND_MAIL_AUTO_LISTE_ETUDIANT_STAGE]) ? boolval($data[ContactTerrainFieldset::SEND_MAIL_AUTO_LISTE_ETUDIANT_STAGE]) : false;
        $mailValidation =  isset($data[ContactTerrainFieldset::SEND_MAIL_AUTO_VALIDATION_STAGE]) ? boolval($data[ContactTerrainFieldset::SEND_MAIL_AUTO_VALIDATION_STAGE]) : false;
        $mailRappelValidation =  isset($data[ContactTerrainFieldset::SEND_MAIL_AUTO_RAPPEL_VALIDATION_STAGE]) ? boolval($data[ContactTerrainFieldset::SEND_MAIL_AUTO_RAPPEL_VALIDATION_STAGE]) : false;

        $contactTerrain->setContact($contact);
        $contactTerrain->setTerrainStage($terrain);
        $contactTerrain->setVisibleParEtudiant($visible);
        $contactTerrain->setIsResponsableStage($responsable);
        $contactTerrain->setCanValiderStage($canValidate);
        $contactTerrain->setSendMailAutoValidationStage($mailValidation);
        $contactTerrain->setSendMailAutoRappelValidationStage($mailRappelValidation);
        $contactTerrain->setIsSignataireConvention($isSingataire);
        $contactTerrain->setPrioriteOrdreSignature($priorite);
        $contactTerrain->setSendMailAutoListeEtudiantsStage($mailListeEtudiants);
        return $contactTerrain;
    }
}