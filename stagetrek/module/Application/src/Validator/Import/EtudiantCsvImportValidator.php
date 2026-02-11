<?php

namespace Application\Validator\Import;

use Application\Entity\Db\Etudiant;
use Application\Exceptions\ImportException;
use Application\Misc\Util;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use Application\Service\Misc\CSVService;
use Application\Validator\Import\Interfaces\AbstractCsvImportValidator;
use DateTime;
use UnicaenUtilisateur\Entity\Db\User;

/**
 * Class EtudiantCsvImportValidator
 */
class EtudiantCsvImportValidator extends AbstractCsvImportValidator
{
    const HEADER_NUM_ETUDIANT = 'Numéro étudiant';
    const HEADER_NOM = "Nom";
    const HEADER_PRENOM = "Prénom";
    const HEADER_EMAIL = "Mail";
    const HEADER_DATE_NAISSANCE = "Date de naissance";
    const HEADER_ADRESSE = "Adresse";
    const HEADER_ADRESSE_COMPLEMENT = "Complément d'adresse";
    const HEADER_CP = "Code postale";
    const HEADER_VILLE = "Ville";
    const HEADER_CEDEX = "Cedex";
    const HEADER_CODE_GROUPE = "Groupe";

    /**
     * @return array
     */
    public static function getImportHeader(): array
    {
        return [
            self::HEADER_NUM_ETUDIANT,
            self::HEADER_NOM,
            self::HEADER_PRENOM,
            self::HEADER_DATE_NAISSANCE,
            self::HEADER_EMAIL,
            self::HEADER_ADRESSE,
            self::HEADER_ADRESSE_COMPLEMENT,
            self::HEADER_CP,
            self::HEADER_VILLE,
            self::HEADER_CEDEX,
            self::HEADER_CODE_GROUPE,
        ];
    }

    public static function isChampsObligatoire(string $key) : bool
    {
        $champsObligatoire = [
            self::HEADER_NUM_ETUDIANT => true,
            self::HEADER_NOM => true,
            self::HEADER_PRENOM => true,
            self::HEADER_EMAIL => true,
        ];
        return ($champsObligatoire[$key]) ?? false;
    }


    protected ?string $numEtu = null;
    protected ?string $nom = null;
    protected ?string $prenom = null;
    protected ?string $dateNaissance = null;
    protected ?string $email = null;
    protected ?string $adresse = null;
    protected ?string $adresseComplement = null;
    protected ?string $cp = null;
    protected ?string $ville = null;
    protected ?string $cedex = null;
    protected ?string $codeGroupe = null;

    public function readData($rowData=[]) : static
    { //Transforme les données au bon types
        $this->numEtu =  trim($this->getCsvService()->readDataAt(self::HEADER_NUM_ETUDIANT, $rowData, ""));
        $this->nom =  trim($this->getCsvService()->readDataAt(self::HEADER_NOM, $rowData, ""));
        $this->prenom =  trim($this->getCsvService()->readDataAt(self::HEADER_PRENOM, $rowData, ""));
        $this->email =  trim($this->getCsvService()->readDataAt(self::HEADER_EMAIL, $rowData, ""));
        $this->dateNaissance =  trim($this->getCsvService()->readDataAt(self::HEADER_DATE_NAISSANCE, $rowData, ""));
        $this->adresse =  trim($this->getCsvService()->readDataAt(self::HEADER_ADRESSE, $rowData, ""));
        $this->adresseComplement =  trim($this->getCsvService()->readDataAt(self::HEADER_ADRESSE_COMPLEMENT, $rowData, ""));
        $this->cp =  trim($this->getCsvService()->readDataAt(self::HEADER_CP, $rowData, ""));
        $this->ville =  trim($this->getCsvService()->readDataAt(self::HEADER_VILLE, $rowData, ""));
        $this->cedex =  trim($this->getCsvService()->readDataAt(self::HEADER_CEDEX, $rowData, ""));
        $this->codeGroupe =  trim($this->getCsvService()->readDataAt(self::HEADER_CODE_GROUPE, $rowData, ""));

        return $this;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    protected function assertRowValidity(array $rowData=[]) : bool
    {
        $this->readData($rowData);
        return $this->assertNumEtu()
            && $this->assertIdentity()
            && $this->assertMail()
            && $this->assertDateNaissance()
            && $this->assertAdresse()
            && $this->assertGroupe();
    }

    //Pour la vérification que plusieurs code/libellé ne sont pas présent de multiple fois dans le fichier
    protected array $etudiantsAdd = [];

    /** @var Etudiant[] $etudiants */
    protected array $etudiants = [];

    protected function getEtudiants(): array
    {
        if (!isset($this->etudiants)) {
            $this->etudiants = $this->getObjectManager()->getRepository(Etudiant::class)->findAll();
        }
        return $this->etudiants;
    }


    private function assertNumEtu() : bool
    {
        $numEtu = $this->numEtu;
        if ($numEtu == "") {
            $msg = "Le numéro d'étudiant n'a pas été fourni";
            throw new ImportException($msg);
        }
        if (strlen($numEtu) > 25) {
            $msg = "Le numéro l'étudiant".Util::POINT_MEDIANT."e ne doit pas dépasser les 25 caractéres";
            throw new ImportException($msg);
        }

        $this->etudiantsAdd[$numEtu] = (isset($this->etudiantsAdd[$numEtu])) ? $this->etudiantsAdd[$numEtu]+1 : 1;
        if($this->etudiantsAdd[$numEtu] >1){
            $msg = sprintf("Le nuémro d'étudiant %s a été défini précédement dans le fichier", $numEtu);
            throw new ImportException($msg);
        }
        return true;
    }
    private function assertIdentity() : bool
    {
        $nom = $this->nom;
        $prenom = $this->prenom;
        if ($nom == "") {
            $msg = "Le nom de l'étudiant".Util::POINT_MEDIANT."e n'a pas été fourni'";
            throw new ImportException($msg);
        }
        if ($prenom == "") {
            $msg = "Le prénom de l'étudiant".Util::POINT_MEDIANT."e n'a pas été fourni'";
            throw new ImportException($msg);
        }
        if (strlen($nom) > 255) {
            $msg = "Le numéro l'étudiant".Util::POINT_MEDIANT."e ne doit pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        if (strlen($prenom) > 255) {
            $msg = "Le prénom l'étudiant".Util::POINT_MEDIANT."e ne doit pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        return true;
    }
    private function assertDateNaissance() : bool
    {
        $date = $this->dateNaissance;
        if(!isset($date)){return true;}
        if($date == ""){return true;}
        /** @var DateTime $dateTest */
        $dateTest = CSVService::textToDate($date);
        if(!isset($dateTest)){
            $msg = "La date de naissance n'est pas au format 'JJ/MM/AAAA'";
            throw new ImportException($msg);
        }
        if($dateTest->format('d/m/Y') != $date){
            $msg = "La date de naissance n'est pas au format 'JJ/MM/AAAA'";
            throw new ImportException($msg);
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertMail() : bool
    {
        $mail = $this->email;
        if (!isset($mail) || $mail == "") {
            $msg = "L'adresse mail n'a pas été définie";
            throw new ImportException($msg);
        }
        if (strlen($mail) > 255 ) {
            $msg = "L'adresse mail ne peux pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
            $msg = "L'adresse mail n'est pas une adresse mail valide";
            throw new ImportException($msg);
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertAdresse() : bool
    {
        if (strlen($this->adresse) > 255 ) {
            $msg = "Le champs adresse ne doit pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        if (strlen($this->adresseComplement) > 255 ) {
            $msg = "Le complément d'adresse ne doit pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        if (strlen($this->ville) > 255 ) {
            $msg = "La ville ne doit pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        if (strlen($this->cp) > 6 ) {
            $msg = "Le code postal ne doit pas dépasser les 6 caractéres";
            throw new ImportException($msg);
        }
        if (strlen($this->cedex) > 25 ) {
            $msg = "Le champ cedex ne doit pas dépasser les 25 caractéres";
            throw new ImportException($msg);
        }
        return true;
    }

    use GroupeServiceAwareTrait;
    private function assertGroupe(): bool
    {
        if (!isset($this->codeGroupe) || $this->codeGroupe =="") {
            return true;
        }
        $groupe = $this->getGroupeService()->findOneBy(['code' => $this->codeGroupe]);
        if(!isset($groupe)){
            $msg = sprintf("Le groupe de code %s n'as pas été trouvée", $this->codeGroupe);
            throw new ImportException($msg);
        }
        return true;
    }
}
