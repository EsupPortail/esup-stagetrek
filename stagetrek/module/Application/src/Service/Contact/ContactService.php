<?php

namespace Application\Service\Contact;

use Application\Entity\Db\Contact;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\Traits\CSVServiceAwareTrait;
use Application\Validator\Import\ContactCsvImportValidator;
use Exception;
use UnicaenAuthentification\Service\Traits\UserContextServiceAwareTrait;

class ContactService extends CommonEntityService
{
    use UserContextServiceAwareTrait;

    public function getEntityClass(): string
    {
        return Contact::class;
    }

    //Trie par défaut par date décroissante
    public function findAll(): array
    {
        $result = $this->getObjectRepository()->findBy([], ['id' => 'asc']);
        return $this->getList($result);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\DBAL\Exception
     */
    public function search(array $criteria): array
    {

        $keys = [
            'code',
            'libelle',
            'nom',
            'mail',
            'telephone',
            'only_actif',
            'actif'
        ];
        $C="c";
        $where = '';
        foreach ($keys as $k) {
            $$k = ($criteria[$k]) ?? null;
        }
        /** @var string $sourceId */
        foreach ($criteria as $k => $v) {
            $sql = '';
            switch ($k) {
                case 'code':
                    $sql = isset($code)
                        ? "lower($C.code) like :$k" : "";
                    break;
                case 'libelle':
                    $sql = isset($libelle)
                        ? "lower($C.libelle) like :$k" : "";
                    break;
                case 'nom':
                    //Split sur le nom pour faire la recherche sans ordre entre les espaces ...
                    if(isset($nom)){
                        $split = explode(' ', $nom);
                        foreach ($split as $i => $sub){
                            $sql .= "lower($C.display_name) like :$k$i AND ";
                        }
                        $sql = substr($sql,0, -5);
                    }
                    break;
                case 'mail':
                    $sql = isset($mail)
                        ? "lower($C.mail) like :$k" : "";
                    break;
                case 'telephone':
                    $sql = isset($telephone)
                        ? "lower($C.telephone) like :$k" : "";
                    break;
                case 'actif':
                    $sql = (isset($actif) && $actif=="true")
                        ? "$C.actif = true" : "";
                    break;
            }
            if($sql) {
                $where .= !$where ? $sql : ' AND ' . $sql;
            }
        } // construction de la requête finale
        $where = ($where) ? "WHERE $where" : $where;
        $sql = "SELECT id from contact $C
            $where";

        $params = [
            "code"                   => trim(strtolower(($code) ?? ""))."%",
            "libelle"                => trim(strtolower(($libelle) ?? ""))."%",
            "mail"  => trim(strtolower(($mail) ?? ""))."%",
            "telephone"  => trim(strtolower(($telephone) ?? ""))."%",
        ];
        //Cas du split sur le displayName
        if(isset($nom)){
            $split = explode(' ', $nom);
            foreach ($split as $i => $sub){
                $params['nom'.$i] = "%".trim(strtolower($sub))."%";
            }
        }
        $ids = $this->getObjectManager()->getConnection()->executeQuery($sql, $params);
        $contacts=[];
        while ($line = $ids->fetchAssociative()) {
            $id = $line['id'];
            /** @var Contact $contact */
            $contact = $this->getObjectManager()->getRepository(Contact::class)->find($id);
            if($contact) {
                $contacts[$id] = $contact;
            }
        }
        return $contacts;
    }


    use CSVServiceAwareTrait;

    /**
     * @desc Hypothèse : la vérification des données par l'importValidateur a déjà été faite
     * @param array $fileData
     * @return Contact[]
     * @throws \Application\Exceptions\ImportException
     */
    public function importFromCSV(array $fileData): array
    {
        $this->getCsvService()->setHeaders(ContactCsvImportValidator::getImportHeader());
        $this->csvService->readCSVFile($fileData);
        $data = $this->getCsvService()->getData();
        $contacts = [];
        foreach ($data as $rowData) {
            $contact = $this->getContactFromData($rowData);
            if($contact->getId() === null){
                $this->getObjectManager()->persist($contact);
            }
            $this->getObjectManager()->flush($contact);
            $contacts[] = $contact;
        }
        return $contacts;
    }

    /**
     * @param mixed $rowData
     * @return \Application\Entity\Db\Contact
     */
    private function getContactFromData(mixed $rowData) : Contact
    {
        $code = trim(($rowData[ContactCsvImportValidator::HEADER_CODE]) ?? "");
        $libelle = trim(($rowData[ContactCsvImportValidator::HEADER_LIBELLE]) ?? "");
        $displayName = trim(($rowData[ContactCsvImportValidator::HEADER_NOM]) ?? "");
        $mail = trim(($rowData[ContactCsvImportValidator::HEADER_MAIl]) ?? "");
        $tel = trim(($rowData[ContactCsvImportValidator::HEADER_TELEPHONE]) ?? "");

        /** @var Contact $contact */
        $contact = $this->getObjectManager()->getRepository(Contact::class)->findOneBy(['code' => $code]);
        if (!$contact) {
            $contact = new Contact();
            $contact->setCode($code);
        }
        $contact->setLibelle($libelle);
        $contact->setDisplayName($displayName);
        $contact->setEmail($mail);
        $contact->setTelephone($tel);
        return $contact;
    }
}