<?php

namespace Application\Validator\Import;

use Application\Entity\Db\Contact;
use Application\Entity\Db\TerrainStage;
use Application\Exceptions\ImportException;
use Application\Service\Misc\CSVService;
use Application\Validator\Import\Interfaces\AbstractCsvImportValidator;
use Exception;

/**
 * Class TerrainStageCsvImportValidator
 */
class ContactTerrainCsvImportValidator extends AbstractCsvImportValidator
{
    CONST HEADER_CODE_CONTACT = "contact";
    CONST HEADER_CODE_TERRAIN = "terrain";
    CONST HEADER_VISIBLE = "visible";
    CONST HEADER_CONVENTION = "convention";
    CONST HEADER_CONVENTION_ORDRE = "convention-ordre";
    CONST HEADER_RESPONSABLE = "responsable";
    CONST HEADER_LISTE_ETUDIANTS = "liste-etudiants";
    CONST HEADER_VALIDEUR = "valideur";

    /**
     * @return array
     */
    public static function getImportHeader() :array
    {
        return [
            self::HEADER_CODE_CONTACT,
            self::HEADER_CODE_TERRAIN,
            self::HEADER_VISIBLE,
            self::HEADER_RESPONSABLE,
            self::HEADER_LISTE_ETUDIANTS,
            self::HEADER_RESPONSABLE,
            self::HEADER_VALIDEUR,
            self::HEADER_CONVENTION,
            self::HEADER_CONVENTION_ORDRE,
        ];
    }



    public static function isChampsObligatoire(string $key) : bool
    {
        $champsObligatoire = [
            self::HEADER_CODE_CONTACT => true,
            self::HEADER_CODE_TERRAIN => true,
        ];
        return ($champsObligatoire[$key]) ?? false;
    }


    protected ?string $codeContact = null;
    protected ?string $codeTerrain = null;
    protected ?bool $visible = null;
    protected ?bool $responsable = null;
    protected ?bool $valideur = null;
    protected ?bool $mailsListeEtudiants = null;
    protected ?bool $signataire = null;
    protected ?bool $ordre = null;

    public function readData($rowData=[]) : static
    { //Transforme les données au bon types
        $this->codeContact = trim(($rowData[self::HEADER_CODE_CONTACT]) ?? "");
        $this->codeTerrain = trim(($rowData[self::HEADER_CODE_TERRAIN]) ?? "");
        $this->visible =  CSVService::yesNoValueToBoolean((isset($rowData[self::HEADER_VISIBLE])) ? $rowData[self::HEADER_VISIBLE] : "", true);
        $this->responsable =  CSVService::yesNoValueToBoolean((isset($rowData[self::HEADER_RESPONSABLE])) ? $rowData[self::HEADER_RESPONSABLE] : "", true);
        $this->valideur =  CSVService::yesNoValueToBoolean((isset($rowData[self::HEADER_VALIDEUR])) ?$rowData[self::HEADER_VALIDEUR] : "", true);
        $this->mailsListeEtudiants =  CSVService::yesNoValueToBoolean((isset($rowData[self::HEADER_LISTE_ETUDIANTS])) ? $rowData[self::HEADER_LISTE_ETUDIANTS] : "", true);
        $this->signataire =  CSVService::yesNoValueToBoolean((isset($rowData[self::HEADER_CONVENTION])) ? $rowData[self::HEADER_CONVENTION] : "", true);
        $this->ordre =  CSVService::textToInt((isset($rowData[self::HEADER_CONVENTION_ORDRE])) ? $rowData[self::HEADER_CONVENTION_ORDRE] : "", 1);
        return $this;
    }

    /****************************
     ** Validation des actions **
     ****************************/
    /**
     * @param array $rowData
     * @return bool
     * @throws Exception
     */
    protected function assertRowValidity(array $rowData=[]) : bool
    {
        $this->readData($rowData);
        return $this->assertContact()
            && $this->assertTerrain()
            && $this->assertProperties()
            && $this->assertConvention();
    }


    /** @var Contact[] $contacts */
    protected array $contacts;
    protected function getContacts() : array
    {
        if(!isset($this->contacts)|| empty($this->contacts)){
            $this->contacts = $this->getObjectManager()->getRepository(Contact::class)->findAll();
        }
        return $this->contacts;
    }

    /**
     * @param string|null $code
     * @return Contact|boolean
     */
    protected function findContactWithCode(?string $code): Contact|bool
    {
        $contacts = $this->getContacts();
        //En pratique il ne devrait y en avoir qu'une mais array_filter retourne un tableau
        $contacts = array_filter($contacts, function (Contact $c) use ($code) {
            return strcmp($c->getCode(), $code) == 0;
        });
        return current($contacts);
    }

    /** @var TerrainStage[] $terrains */
    protected array $terrains = [];
    protected function getTerrains() : array
    {
        if(!isset($this->terrains)|| empty($this->terrains)){
            $this->terrains = $this->getObjectManager()->getRepository(TerrainStage::class)->findAll();
        }
        return $this->terrains;
    }

    /**
     * @param string|null $code
     * @return Contact|boolean
     */
    protected function findTerrainWithCode(?string $code): TerrainStage|bool
    {
        $terrains = $this->getTerrains();
        //En pratique il ne devrait y en avoir qu'une mais array_filter retourne un tableau
        $terrains = array_filter($terrains, function (TerrainStage $t) use ($code) {
            return strcmp($t->getCode(), $code) == 0;
        });
        return current($terrains);
    }


    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertContact() : bool
    {
        $code = $this->codeContact;
//        PAs de code = nouveaux contact
        if (!isset($code) ||  $code == "") {
            $msg = "Le code du contact n'a pas été défini";
            throw new ImportException($msg);
        }
        $contact = $this->findContactWithCode($code);
        if(!$contact){
            $msg = sprintf("Le contact de code %s n'existe pas.", $code);
            throw new ImportException($msg);
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertTerrain() : bool
    {
        $code = $this->codeTerrain;
//        PAs de code = nouveaux contact
        if (!isset($code) ||  $code == "") {
            $msg = "Le code du terrain n'a pas été défini";
            throw new ImportException($msg);
        }
        $terrain = $this->findTerrainWithCode($code);
        if(!$terrain){
            $msg = sprintf("Le terrain de stage de code %s n'existe pas.", $code);
            throw new ImportException($msg);
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertProperties() : bool
    {
        if($this->visible === null){
            $msg = "Le champs ". self::HEADER_VISIBLE ." ne contient pas l'une des valeurs attendues : Oui/Non (ou vide)";
            throw new ImportException($msg);
        }
        if($this->responsable === null){
            $msg = "Le champs ". self::HEADER_RESPONSABLE ." ne contient pas l'une des valeurs attendues : Oui/Non (ou vide)";
            throw new ImportException($msg);
        }
        if($this->valideur === null){
            $msg = "Le champs ". self::HEADER_VALIDEUR ." ne contient pas l'une des valeurs attendues : Oui/Non (ou vide)";
            throw new ImportException($msg);
        }
        if($this->mailsListeEtudiants === null){
            $msg = "Le champs ". self::HEADER_LISTE_ETUDIANTS ." ne contient pas l'une des valeurs attendues : Oui/Non (ou vide)";
            throw new ImportException($msg);
        }
        if($this->valideur){
            $code = $this->codeContact;
            $contact = $this->findContactWithCode($code);
            $mail = $contact->getEmail();
            if(!isset($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)){
                $msg = "L'adresse mail du contact n'est pas correctement définie. Il ne peut pas recevoir les liens de validations.";
                throw new ImportException($msg);
            }
        }
        if($this->mailsListeEtudiants){
            $code = $this->codeContact;
            $contact = $this->findContactWithCode($code);
            $mail = $contact->getEmail();
            if(!isset($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)){
                $msg = "L'adresse mail du contact n'est pas correctement définie. Il ne peut pas recevoir la liste des étudiants.";
                throw new ImportException($msg);
            }
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertConvention() : bool
    {
        if($this->signataire === null){
            $msg = "Le champs ". self::HEADER_CONVENTION ." ne contient pas l'une des valeurs attendues : Oui/Non (ou vide)";
            throw new ImportException($msg);
        }
        if($this->signataire && $this->ordre === null){
            $msg = "L'ordre de signature du contact n'est pas définie";
            throw new ImportException($msg);
        }
        if($this->signataire && $this->ordre <= 0){
            $msg = "L'ordre de signature du contact n'est pas un entier positif ou null";
            throw new ImportException($msg);
        }
        return true;
    }

}
