<?php

namespace Application\Validator\Import;

use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\TerrainStage;
use Application\Exceptions\ImportException;
use Application\Service\Misc\CSVService;
use Application\Validator\Import\Interfaces\AbstractCsvImportValidator;
use Exception;

/**
 * Class TerrainStageCsvImportValidator
 */
class TerrainStageCsvImportValidator extends AbstractCsvImportValidator
{
    CONST HEADER_CODE_CATEGORIE = "categorie";
    CONST HEADER_CODE_TERRAIN = "code";
    CONST HEADER_LIBELLE = "libelle";
    const HEADER_SERVICE = "service";
    CONST HEADER_CAPA_MIN = "min_place";
    CONST HEADER_CAPA_IDEAL = "ideal_place";
    CONST HEADER_CAPA_MAX = "max_place";
    CONST HEADER_PREFERENCES = "preferences";
    CONST HEADER_HORS_SUBDIVISION = "hors_subdivision";
    CONST HEADER_LIEN = "lien";
    CONST HEADER_ADRESSE = "adresse";
    CONST HEADER_ADRESSE_COMPLEMENT = "adresse_complement";
    CONST HEADER_CP = "cp";
    CONST HEADER_VILLE = "ville";
    CONST HEADER_CEDEX = "cedex";

    /**
     * @return array
     */
    public static function getImportHeader() :array
    {
        return [
            self::HEADER_CODE_CATEGORIE,
            self::HEADER_CODE_TERRAIN,
            self::HEADER_LIBELLE,
            self::HEADER_CAPA_MIN,
            self::HEADER_CAPA_IDEAL,
            self::HEADER_CAPA_MAX,
            self::HEADER_HORS_SUBDIVISION,
            self::HEADER_PREFERENCES,
            self::HEADER_SERVICE,
            self::HEADER_LIEN,
            self::HEADER_ADRESSE,
            self::HEADER_ADRESSE_COMPLEMENT,
            self::HEADER_CP,
            self::HEADER_VILLE,
            self::HEADER_CEDEX,
        ];
    }


    public static function isChampsObligatoire(string $key) : bool
    {
        $champsObligatoire = [
            self::HEADER_CODE_CATEGORIE => true,
            self::HEADER_LIBELLE => true,
        ];
        return ($champsObligatoire[$key]) ?? false;
    }


    protected bool $modeEdit = false;
    protected ?string $codeCategorie = null;
    protected ?string $codeTerrain = null;
    protected ?string $libelle = null;
    protected ?string $service = null;
    protected ?int $min = null;
    protected ?int $ideal = null;
    protected ?int $max = null;
    protected ?bool $horsSubdivision = null;
    protected ?bool $allowPreferences = null;
    protected ?string $lien = null;
    protected ?string $adresse = null;
    protected ?string $adresseComplement = null;
    protected ?string $cp = null;
    protected ?string $ville = null;
    protected ?string $cedex = null;

    public function readData($rowData=[]) : static
    { //Transforme les données au bon types
        $this->codeCategorie = trim($this->getCsvService()->readDataAt(self::HEADER_CODE_CATEGORIE, $rowData, ""));
        $this->codeTerrain = trim($this->getCsvService()->readDataAt(self::HEADER_CODE_TERRAIN, $rowData, ""));
        $this->libelle = trim($this->getCsvService()->readDataAt(self::HEADER_LIBELLE, $rowData, ""));
        $this->service = trim($this->getCsvService()->readDataAt(self::HEADER_SERVICE, $rowData, ""));
        $this->min = CSVService::textToInt($this->getCsvService()->readDataAt(self::HEADER_CAPA_MIN, $rowData),0);
        $this->ideal = CSVService::textToInt($this->getCsvService()->readDataAt(self::HEADER_CAPA_IDEAL, $rowData),0);
        $this->max = CSVService::textToInt($this->getCsvService()->readDataAt(self::HEADER_CAPA_MAX, $rowData),0);
        $this->horsSubdivision = CSVService::yesNoValueToBoolean($this->getCsvService()->readDataAt(self::HEADER_HORS_SUBDIVISION, $rowData, ""), false);
        $this->allowPreferences = CSVService::yesNoValueToBoolean($this->getCsvService()->readDataAt(self::HEADER_PREFERENCES, $rowData, ""), true);
        $this->lien = trim($this->getCsvService()->readDataAt(self::HEADER_LIEN, $rowData, ""));
        $this->adresse = trim($this->getCsvService()->readDataAt(self::HEADER_ADRESSE, $rowData, ""));
        $this->adresseComplement = trim($this->getCsvService()->readDataAt(self::HEADER_ADRESSE_COMPLEMENT, $rowData, ""));
        $this->cp = trim($this->getCsvService()->readDataAt(self::HEADER_CP, $rowData, ""));
        $this->ville = trim($this->getCsvService()->readDataAt(self::HEADER_VILLE, $rowData, ""));
        $this->cedex = trim($this->getCsvService()->readDataAt(self::HEADER_CEDEX, $rowData, ""));
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
        return $this->assertCategorie()
        && $this->assertTerrain()
        && $this->assertLibelle()
        && $this->assertPlaces()
        && $this->assertProperties()
        && $this->assertAdresse();
    }

    /** @var CategorieStage[] $categoriesStages */
    protected array $categoriesStages = [];
    protected function getCategoriesStages(): array
    {
        if(!isset($this->categoriesStages) || empty($this->categoriesStages)){
            $this->categoriesStages = $this->getObjectManager()->getRepository(CategorieStage::class)->findAll();
        }
        return $this->categoriesStages;
    }

    /** @var TerrainStage[] $terrainsStages */
    protected array $terrainsStages = [];
    protected function getTerrainsStages(): array
    {
        if(!isset($this->terrainsStages)){
            $this->terrainsStages = $this->getObjectManager()->getRepository(TerrainStage::class)->findAll();
        }
        return $this->terrainsStages;
    }

    /**
     * @param string $code
     * @return CategorieStage|boolean
     */
    protected function findCategorieWithCode(string $code): CategorieStage|bool
    {
        $categories = $this->getCategoriesStages();
        //En pratique il ne devrait y en avoir qu'une mais array_filter retourne un tableau
        $categorie = array_filter($categories, function (CategorieStage $c) use ($code) {
            return strcmp($c->getCode(), $code) == 0;
        });
        return current($categorie);
    }

    /**
     * @param string $code
     * @return TerrainStage|boolean
     */
    protected function findTerrainWithCode(string $code): ?TerrainStage
    {
        $terrains = $this->getTerrainsStages();
        //En pratique il ne devrait y en avoir qu'une mais array_filter retourne un tableau
        $terrain = array_filter($terrains, function (TerrainStage $t) use ($code) {
            return strcmp($t->getCode(), $code) == 0;
        });
        if(empty($terrain)){return null;}
        return current($terrain);
    }

    /**
     * @param string $libelle
     * @return TerrainStage|boolean
     */
    protected function findTerrainWithLibelle(string $libelle): bool|TerrainStage
    {
        $terrains = $this->getTerrainsStages();
        $terrain = array_filter($terrains, function (TerrainStage $t) use ($libelle) {
            return strcmp($t->getLibelle(), $libelle) == 0;
        });
        return current($terrain);
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertCategorie() : bool
    {
        $code = $this->codeCategorie;
        //Vérification sur la présence des données requises
        if (!isset($code) ||  $code == "") {
            $msg = "Le code de la catégorie de stage n'a pas été défini";
            throw new ImportException($msg);
        }
        $categorie = $this->findCategorieWithCode($code);
        if(!$categorie){
            $msg = sprintf("La catégorie de stage de code %s n'existe pas.", $code);
            throw new ImportException($msg);
        }
        return true;
    }

    //Pour la vérification que plusieurs code/libellé ne sont pas présent de multiple fois dans le fichier
    protected array $listeCodeTerrainAdd = [];

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertTerrain() : bool
    {
        $code = $this->codeTerrain;
        if($code==""){return true;}
        $codeCategorie = $this->codeCategorie;
        $terrain = $this->findTerrainWithCode($code);
//        if (!$terrain) {
//            $msg = sprintf("Le terrain de code %s n'existe pas.", $code);
//            throw new ImportException($msg);
//        }

        if(isset($terrain) && $terrain->getCategorieStage()->getCode() != $codeCategorie){
            $msg = sprintf("Le terrain de stage avec le code %s existe déjà mais est défini pour la catégorie de stage de code %s (%s)",
                $code, $terrain->getCategorieStage()->getCode(), $terrain->getCategorieStage()->getLibelle());
            throw new ImportException($msg);
        }

        $this->listeCodeTerrainAdd[$code] = (isset($this->listeCodeTerrainAdd[$code])) ? $this->listeCodeTerrainAdd[$code]+1 : 1;
        if($this->listeCodeTerrainAdd[$code] >1){
            $msg = sprintf("Le code de terrain %s a été défini précédement dans le fichier", $code);
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
        $codeTerrain = $this->codeTerrain;
        if (!isset($libelle) || $libelle == "") {
            $msg = "Le libellé du terrain n'a pas été défini";
            throw new ImportException($msg);
        }
        if (strlen($libelle) > 255 ) {
            $msg = "Le libellé ne doit pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        $terrainExiste = $this->findTerrainWithLibelle($libelle);
        if($terrainExiste && $terrainExiste->getCode() != $codeTerrain){
            $msg = sprintf("Un terrain de stage avec le libellé %s existe déjà", $libelle);
            throw new ImportException($msg);
        }

        $this->listeLibelleAdd[$libelle] = (isset($this->listeLibelleAdd[$libelle])) ? $this->listeLibelleAdd[$libelle]+1 : 1;
        if($this->listeLibelleAdd[$libelle] >1){
            $msg = sprintf("Le libellé %s a été défini précédement dans le fichier", $this->libelle);
            throw new ImportException($msg);
        }

        return true;
    }


    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertPlaces() : bool
    {
        if ($this->min < 0) {
            $msg = "La capacité minimale n'est pas un entier positif ou nul.";
            throw new ImportException($msg);
        }
        if ($this->ideal < 0) {
            $msg = "La capacité idéale n'est pas un entier positif ou nul.";
            throw new ImportException($msg);
        }
        if ($this->max < 0) {
            $msg = "La capacité maximale n'est pas un entier positif ou nul.";
            throw new ImportException($msg);
        }
        if ($this->min > $this->ideal) {
            $msg = "La capacité minimale doit être inférieur ou égale à la capacité idéale";
            throw new ImportException($msg);
        }
        if ($this->ideal > $this->max) {
            $msg = "La capacité idéale doit être inférieur ou égale à la capacité maximale";
            throw new ImportException($msg);
        }
        return true;
    }

    /**
     * @throws \Application\Exceptions\ImportException
     */
    private function assertProperties() : bool
    {
        if($this->horsSubdivision === null){
            $msg = "Le champs ". self::HEADER_HORS_SUBDIVISION ." ne contient pas l'une des valeurs attendues : Oui/Non (ou vide)";
            throw new ImportException($msg);
        }
        if($this->allowPreferences === null){
            $msg = "Le champs ". self::HEADER_PREFERENCES ." ne contient pas l'une des valeurs attendues : Oui/Non (ou vide)";
            throw new ImportException($msg);
        }

        if (isset($this->service) && strlen($this->service) > 255 ) {
            $msg = "Le nom du service ne doit pas dépasser les 255 caractéres";
            throw new ImportException($msg);
        }
        if (isset($this->lien) && strlen($this->lien) > 255 ) {
            $msg = "Le lien ne doit pas dépasser les 255 caractéres";
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

}
