<?php

namespace Application\Validator\Import;

use Application\Entity\Db\CategorieStage;
use Application\Exceptions\ImportException;
use Application\Service\Misc\CSVService;
use Application\Validator\Import\Interfaces\AbstractCsvImportValidator;
use Exception;

/**
 * Class TerrainStageCsvImportValidator
 */
class CategorieStageCsvImportValidator extends AbstractCsvImportValidator
{
    CONST HEADER_CODE_CATEGORIE = "code";
    CONST HEADER_ACRONYME = "acronyme";
    CONST HEADER_LIBELLE = "libelle";
    CONST HEADER_ORDRE = "ordre";
    CONST HEADER_PRINCIPAL = "princpal";

    /**
     * @return array
     */
    public static function getImportHeader() :array
    {
        return [
            self::HEADER_CODE_CATEGORIE,
            self::HEADER_ACRONYME,
            self::HEADER_LIBELLE,
            self::HEADER_ORDRE,
            self::HEADER_PRINCIPAL,
        ];
    }

    protected ?string $code = null;
    protected ?string $acronyme = null;
    protected ?string $libelle = null;
    protected ?int $ordre = null;
    protected ?bool $principal = null;

    public function readData($rowData=[]) : static
    { //Transforme les données au bon types
        $this->code = trim($this->getCsvService()->readDataAt(self::HEADER_CODE_CATEGORIE, $rowData, ""));
        $this->acronyme = trim($this->getCsvService()->readDataAt(self::HEADER_ACRONYME, $rowData, ""));
        $this->libelle = trim($this->getCsvService()->readDataAt(self::HEADER_LIBELLE, $rowData, ""));
        $this->ordre = CSVService::textToInt($this->getCsvService()->readDataAt(self::HEADER_ORDRE, $rowData, 0));
        $this->principal = CSVService::yesNoValueToBoolean($this->getCsvService()->readDataAt(self::HEADER_PRINCIPAL, $rowData, false));
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
        return $this->assertCode()
        && $this->assertAcronyme()
        && $this->assertLibelle()
        && $this->assertOrdre()
        && $this->assertProperties();
    }

    /** @var CategorieStage[] $categoriesStages */
    protected array $categoriesStages;

    protected function getCategoriesStages() : array
    {
        if(!isset($this->categoriesStages)){
            $this->categoriesStages = $this->getObjectManager()->getRepository(CategorieStage::class)->findAll();
        }
        return $this->categoriesStages;
    }

    /**
     * @param string|null $code
     * @return CategorieStage|boolean
     */
    protected function findCategorieWithCode(?string $code): CategorieStage|bool
    {
        $categories = $this->getCategoriesStages();
        //En pratique il ne devrait y en avoir qu'une mais array_filter retourne un tableau
        $categorie = array_filter($categories, function (CategorieStage $c) use ($code) {
            return strcmp($c->getCode(), $code) == 0;
        });
        return current($categorie);
    }

    /**
     * @param string|null $libelle
     * @return CategorieStage|boolean
     */
    protected function findCategorieWithLibelle(?string $libelle): CategorieStage|bool
    {
        $categories = $this->getCategoriesStages();
        $categories = array_filter($categories, function (CategorieStage $c) use ($libelle) {
            return strcmp($c->getLibelle(), $libelle) == 0;
        });
        return current($categories);
    }

    /**
     * @param string|null $acronyme
     * @return CategorieStage|boolean
     */
    protected function findCategorieWithAcronyme(?string $acronyme) : CategorieStage|bool
    {
        $categories = $this->getCategoriesStages();
        $categories = array_filter($categories, function (CategorieStage $c) use ($acronyme) {
            return strcmp($c->getAcronyme(), $acronyme) == 0;
        });
        return current($categories);
    }

    //Pour la vérification que plusieurs code/libellé ne sont pas présent de multiple fois dans le fichier
    protected array $listeCodeCategorieAdd = [];

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertCode() : bool
    {
        $code = $this->code;
        if($code==""){return true;}
        //autogénération du code
//        Inutile en fait car si non vide, on peut creer la catégorie en spécifiant le code
//        $categorie = $this->findCategorieWithCode($code);
        //Implique une création avec un code précis
//        if (!$categorie) {return true;}

        $this->listeCodeCategorieAdd[$code] = (isset($this->listeCodeCategorieAdd[$code])) ? $this->listeCodeCategorieAdd[$code]+1 : 1;
        if($this->listeCodeCategorieAdd[$code] >1){
            $msg = sprintf("Le code %s a été défini précédement dans le fichier", $code);
            throw new ImportException($msg);
        }
        return true;
    }

    protected array $listeLibelleAdd = [];

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertLibelle() : bool
    {
        $libelle = $this->libelle;
        $code = $this->code;
        if (!isset($libelle) || $libelle == "") {
            $msg = "Le libellé de la catégorie n'a pas été défini";
            throw new ImportException($msg);
        }
        if (strlen($libelle) > 255 ) {
            $msg = "Le libellé ne doit pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        $categorieExite = $this->findCategorieWithLibelle($libelle);
        if($categorieExite && $categorieExite->getCode() != $code){
            $msg = sprintf("Une catégorie de stage avec le libellé %s existe déjà", $libelle);
            throw new ImportException($msg);
        }

        $this->listeLibelleAdd[$libelle] = (isset($this->listeLibelleAdd[$libelle])) ? $this->listeLibelleAdd[$libelle]+1 : 1;
        if($this->listeLibelleAdd[$libelle] >1){
            $msg = sprintf("Le libellé %s a été défini précédement dans le fichier", $this->libelle);
            throw new ImportException($msg);
        }

        return true;
    }

    protected array $listeAcronymeAdd = [];

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertAcronyme() : bool
    {
        $acronyme = $this->acronyme;
        $code = $this->code;
        if (!isset($acronyme) || $acronyme == "") {
            $msg = "L'acronyme n'a pas été défini";
            throw new ImportException($msg);
        }
        if (strlen($acronyme) > 10 ) {
            $msg = "L'acronyme ne doit pas dépasser les 10 caractéres";
            throw new ImportException($msg);
        }
        $categorieExite = $this->findCategorieWithAcronyme($acronyme);
        if($categorieExite && $categorieExite->getCode() != $code){
            $msg = sprintf("Une catégorie de stage avec l'acronyme %s existe déjà", $acronyme);
            throw new ImportException($msg);
        }

        $this->listeAcronymeAdd[$acronyme] = (isset($this->listeAcronymeAdd[$acronyme])) ? $this->listeAcronymeAdd[$acronyme]+1 : 1;
        if($this->listeAcronymeAdd[$acronyme] >1){
            $msg = sprintf("L'acronyme %s a été défini précédement dans le fichier", $this->acronyme);
            throw new ImportException($msg);
        }
        return true;
    }


    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertOrdre() : bool
    {
        if ($this->ordre < 0) {
            $msg = "L'ordre d'affichage n'est pas un entier positif ou nul.";
            throw new ImportException($msg);
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertProperties() : bool
    {
        if($this->principal === null){
            $msg = "Le champs ". self::HEADER_PRINCIPAL ." ne contient pas l'une des valeurs attendues : Oui/Non";
            throw new ImportException($msg);
        }
        return true;
    }

}
