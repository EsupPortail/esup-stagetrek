<?php

namespace Application\Service\Contact;

use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\TerrainStage;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\CSVService;
use Application\Service\Misc\Traits\CSVServiceAwareTrait;
use Application\Validator\Import\ContactTerrainCsvImportValidator;

class ContactTerrainService extends CommonEntityService
{
    public function getEntityClass(): string
    {
        return ContactTerrain::class;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\DBAL\Exception
     */
    public function search(array $criteria): array
    {

        $keys = [
            'contact',
            'terrain',
            'categorie',
        ];
        $C="c";
        $T="t";
        $where = '';
        foreach ($keys as $k) {
            $$k = ($criteria[$k]) ?? null;
        }
        /** @var string $sourceId */
        foreach ($criteria as $k => $v) {
            $sql = '';
            switch ($k) {
                case 'contact':
                    $sql = isset($contact)
                        ? "$C.contact_id = :$k" : "";
                    break;
                case 'terrain':
                    $sql = isset($terrain)
                        ? "$T.id = :$k" : "";
                break;
                case 'categorie':
                    $sql = isset($categorie)
                        ? "$T.categorie_stage_id = :$k" : "";
                break;
            }
            if($sql) {
                $where .= !$where ? $sql : ' AND ' . $sql;
            }
        } // construction de la requête finale
        $where = ($where) ? "WHERE $where" : $where;
        $sql = "SELECT $C.id from contact_terrain $C
            left join terrain_stage $T on $C.terrain_stage_id = $T.id
            $where";

        $params = [
            "contact"                   => intval(trim($contact)),
            "terrain"                   => intval(trim($terrain)),
            "categorie"                   => intval(trim($categorie)),
        ];
        $ids = $this->getObjectManager()->getConnection()->executeQuery($sql, $params);
        $contacts=[];
        while ($line = $ids->fetchAssociative()) {
            $id = $line['id'];
            /** @var Contact $contact */
            $contact = $this->getObjectManager()->getRepository(ContactTerrain::class)->find($id);
            if($contact) {
                $contacts[$id] = $contact;
            }
        }
        return $contacts;
    }

    //Trie par défaut par date décroissante
    public function findAll(): array
    {
        return $this->getObjectRepository()->findBy([], ['id' => 'asc']);
    }

    use CSVServiceAwareTrait;

    /**
     * @throws \Application\Exceptions\ImportException
     */
    public function importFromCSV(array $fileData): array
    {
        $this->getCsvService()->setHeaders(ContactTerrainCsvImportValidator::getImportHeader());
        $this->csvService->readCSVFile($fileData);
        $data = $this->getCsvService()->getData();
        $contacts = [];
        foreach ($data as $rowData) {
            $contactTerrain = $this->getContactTerrainFromData($rowData);
            if($contactTerrain->getId() === null){
                $this->getObjectManager()->persist($contactTerrain);
            }
            $this->getObjectManager()->flush($contactTerrain);
            $contacts[] = $contactTerrain;
        }
        return $contacts;
    }

    /**
     * @param mixed $rowData
     * @return \Application\Entity\Db\ContactTerrain
     */
    private function getContactTerrainFromData(mixed $rowData) : ContactTerrain
    {
        $codeContact = trim(($rowData[ContactTerrainCsvImportValidator::HEADER_CODE_CONTACT]) ?? "");
        $codeTerrain = trim(($rowData[ContactTerrainCsvImportValidator::HEADER_CODE_TERRAIN]) ?? "");
        /** @var Contact $contact */
        $contact = $this->getObjectManager()->getRepository(Contact::class)->findOneBy(['code' => $codeContact]);
         /** @var TerrainStage $terrain */
        $terrain = $this->getObjectManager()->getRepository(TerrainStage::class)->findOneBy(['code' => $codeTerrain]);

        $ct = $contact->getContactForTerrain($terrain);
        if(!isset($ct)){
           $ct = new ContactTerrain();
           $ct->setContact($contact);
           $ct->setTerrainStage($terrain);
        }
        $visible =  CSVService::yesNoValueToBoolean($rowData[ContactTerrainCsvImportValidator::HEADER_VISIBLE] ?? "oui", true);
        $responsable =  CSVService::yesNoValueToBoolean($rowData[ContactTerrainCsvImportValidator::HEADER_RESPONSABLE] ?? "oui", true);
        $valideur =  CSVService::yesNoValueToBoolean($rowData[ContactTerrainCsvImportValidator::HEADER_VALIDEUR] ?? "oui", true);
        $mailsListeEtudiants =  CSVService::yesNoValueToBoolean($rowData[ContactTerrainCsvImportValidator::HEADER_LISTE_ETUDIANTS] ?? "oui", true);
        $signataire =  CSVService::yesNoValueToBoolean($rowData[ContactTerrainCsvImportValidator::HEADER_CONVENTION] ?? "oui", true);
        $ordre =  CSVService::textToInt($rowData[ContactTerrainCsvImportValidator::HEADER_CONVENTION_ORDRE] ?? 1, 1);

        $ct->setVisibleParEtudiant($visible);
        $ct->setIsResponsableStage($responsable);
        $ct->setCanValiderStage($valideur);
        $ct->setSendMailAutoListeEtudiantsStage($mailsListeEtudiants);
        $ct->setSendMailAutoValidationStage($valideur);
        $ct->setSendMailAutoRappelValidationStage($valideur);
        $ct->setIsSignataireConvention($signataire);
        $ct->setPrioriteOrdreSignature(($signataire) ? $ordre : null);

        return $ct;
    }
}