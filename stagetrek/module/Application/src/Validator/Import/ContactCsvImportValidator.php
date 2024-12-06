<?php

namespace Application\Validator\Import;

use Application\Exceptions\ImportException;
use Application\Validator\Import\Interfaces\AbstractCsvImportValidator;
use Exception;

/**
 * Class TerrainStageCsvImportValidator
 */
class ContactCsvImportValidator extends AbstractCsvImportValidator
{
    CONST HEADER_CODE = "code";
    CONST HEADER_LIBELLE = "libelle";
    const HEADER_NOM = "nom";
    const HEADER_MAIl = "mail";
    const HEADER_TELEPHONE = "telephone";

    /**
     * @return array
     */
    public static function getImportHeader() :array
    {
        return [
            self::HEADER_CODE,
            self::HEADER_LIBELLE,
            self::HEADER_NOM,
            self::HEADER_MAIl,
            self::HEADER_TELEPHONE,
        ];
    }

    protected ?string $code = null;
    protected ?string $libelle = null;
    protected ?string $nom = null;
    protected ?string $mail = null;
    protected ?string $telephone = null;

    public function readData($rowData=[]) : static
    { //Transforme les données au bon types
        $this->code = trim(($rowData[self::HEADER_CODE]) ?? "");
        $this->libelle = trim(($rowData[self::HEADER_LIBELLE]) ?? "");
        $this->nom = trim(($rowData[self::HEADER_NOM]) ?? "");
        $this->mail = trim(($rowData[self::HEADER_MAIl]) ?? "");
        $this->telephone = trim(($rowData[self::HEADER_TELEPHONE]) ?? "");
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
//        TODO : des traits pour les assertions commune (ie : mail) ? pas sur car chaque cas sera probablement spécifique
        $this->readData($rowData);
        return $this->assertCode()
        && $this->assertLibelle()
        && $this->assertDisplayName()
        && $this->assertMail()
        && $this->assertTelephone();
    }

    protected array $listeCodeAdd = [];

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertCode() : bool
    {
        $code = $this->code;
//        PAs de code = nouveaux contact
        if (!isset($code) ||  $code == "") {return true; }
        if (strlen($code) > 50 ) {
            $msg = "Le code ne doit pas dépasser les 50 caractéres";
            throw new ImportException($msg);
        }

        $this->listeCodeAdd[$code] = (isset($this->listeCodeAdd[$code])) ? $this->listeCodeAdd[$code]+1 : 1;
        if($this->listeCodeAdd[$code] >1){
            $msg = sprintf("Le code %s a été défini précédement dans le fichier", $code);
            throw new ImportException($msg);
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertLibelle() : bool
    {
        $libelle = $this->libelle;
        if (!isset($libelle) || $libelle == "") {
            $msg = "Le libellé / la fonction du contact n'a pas été défini";
            throw new ImportException($msg);
        }
        if (strlen($libelle) > 255 ) {
            $msg = "Le libellé / la fonction ne doit pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertDisplayName() : bool
    {
        $displayName = $this->nom;
        if (!isset($displayName) || $displayName == "") {
            $msg = "Le nom, prénom du contact n'ont pas été défini";
            throw new ImportException($msg);
        }
        if (strlen($displayName) > 255 ) {
            $msg = "Le nom, prénom du contact ne doivent pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertMail() : bool
    {
        $mail = $this->mail;
        if (!isset($mail) || $mail == "") {
            $msg = "L'adresse mail du contact n'a pas été définie";
            throw new ImportException($msg);
        }
        if (strlen($mail) > 255 ) {
            $msg = "L'adresse mail du contact ne peux pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
            $msg = "L'adresse mail du contact n'est pas une adresse mail valide";
            throw new ImportException($msg);
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertTelephone() : bool
    {
        $tel = $this->telephone;
        if (!isset($tel) || $tel == "") {return true;}
        if (strlen($tel) > 25 ) {
            $msg = "Le numéro de téléphone ne peux pas dépasser les 25 caractéres";
            throw new ImportException($msg);
        }
        return true;
    }
}
