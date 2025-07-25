<?php


namespace Application\Service\Misc;


use Application\Exceptions\ImportException;
use Application\Misc\Util;
use DateTime;
use Exception;
use UnicaenApp\View\Model\CsvModel;

class CSVService
{
    const SEPARATOR = ';';

    public function __construct(){
        $this->fileName="";
        $this->separator=self::SEPARATOR;
        $this->enclosure="\"";
        $this->headers=[];
        $this->data=[];
        $this->mimeType = ['text/x-csv', 'text/csv', 'application/csv', 'application/vnd.ms-excel', 'application/x-csv', 'text/comma-separated-values', 'text/x-comma-separated-values', 'text/tab-separated-values'];
    }

    /** @var string $fileName */
    protected string $fileName="";
    /** @var string $separator */
    protected string $separator = ";";
    /** @var string $enclosure */
    protected string $enclosure = "\"";
    /** @var array */
    protected array $headers = [];

    protected array $mimeType = [];

    /** @var array */
    protected array $data = [];

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return \Application\Service\Misc\CSVService
     */
    public function setFileName(string $fileName) : static
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     * @return \Application\Service\Misc\CSVService
     */
    public function setSeparator(string $separator): static
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnclosure(): string
    {
        return $this->enclosure;
    }

    /**
     * @param string $enclosure
     * @return \Application\Service\Misc\CSVService
     */
    public function setEnclosure(string $enclosure): static
    {
        $this->enclosure = $enclosure;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return \Application\Service\Misc\CSVService
     */
    public function setHeaders(array $headers) : static
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return \Application\Service\Misc\CSVService
     */
    public function setData(array $data) : static
    {
        $this->data=[];
        foreach ($data as $rowData){
            $this->addData($rowData);
        }
        return $this;
    }

    /**
     * @param array $data
     * @return \Application\Service\Misc\CSVService
     */
    public function addData(array $data=[]) : static
    {
        $this->data[] = $data;

        return $this;
    }

    /** @return CsvModel */
    public function getCSVModel(): CsvModel
    {
        $CSV = new CsvModel();
        $CSV->setDelimiter($this->separator);
        $CSV->setEnclosure($this->enclosure);
        $CSV->setHeader($this->headers);
        //On ne prend que les données issue du headers
        $records = [];
        foreach ($this->data as $rowData){
            $item  = [];
            foreach ($this->headers as $key){
                $item[]  = (key_exists($key, $rowData)) ? $rowData[$key] : "";
            }
            $records[] = $item;
        }
        $CSV->setData($records);
        $CSV->setFilename($this->fileName);
        return $CSV;
    }

    /** array $fileName
     * @throws \Application\Exceptions\ImportException
     */
    public function readCSVFile(array $fileData=[]): static
    {
        if(!key_exists("type", $fileData) || !in_array($fileData["type"], $this->mimeType)){
            throw new ImportException("Le fichier fournis n'est pas un CSV valide.");
        }
        $fileName = key_exists("tmp_name", $fileData) ? $fileData["tmp_name"] : null;
        if(!$fileName){
            throw new ImportException("Le fichier fournis n'a pas été correctement chargé.");
        }
        $csv =  array_map(
            function($data) { return str_getcsv($data, $this->separator, $this->enclosure);}
            , file($fileName)
        );
        if($csv==[]){
            throw new ImportException("Le fichier fournis est vide");
        }
        //Vérification du header et récupération de la colonne pour chaque entête
        $headerPosition = [];
        $headers = [];
        foreach($this->getHeaders() as $h){
            $h = $this::formatHeader($h);
            $headers[$h] = $h;
        }
        foreach (array_shift($csv) as $col => $h){
            $h = $this::formatHeader($h);
            if(in_array($h, $headers)) {
                $headerPosition[$h] = $col;
            }
        }
//        if(sizeOf($headerPosition) != sizeOf($this->headers)){
//            $msg = sprintf("L'entête du CSV n'est pas valide. Merci de respecter le modéle d'importation : <b>%s</b>",
//                implode($this->separator, $this->headers));
//            throw new ImportException($msg);
//        }
        //On récupére les données
        $this->data=[];
        $ligne=1;
        foreach ($csv as $row) {
            $ligne++;
            if(implode("", $row) == "") continue; //Ligne vide, on l'ignore
            $item =[];
            foreach($headerPosition as $key => $col){
                $key = $this::formatHeader($key);
                if(key_exists($col, $row)){
                    $item[$key] = $row[$col];
                }
                else{
                    $item[$key] = "";
                }
            }
            $this->data[$ligne]=$item;
        }
        return $this;
    }

    public static function formatHeader(string $key): string
    { //Pour ne pas avoir de pb d'espace/maj et autres
        $key = trim(strtolower($key));
        $key = Util::removeAccents($key);
        $key = str_replace(" ", '', $key);
        $key = str_replace("'", '', $key);
        $key = str_replace("\"", '', $key);
        return $key;
    }

    /** Retourne la valeur d'une ligne à une clé précise */
    public function readDataAt(string $key, array $row, mixed $default=null): mixed
    {
        $key = self::formatHeader($key);
        return (isset($row[$key])) ? $row[$key] : $default;
    }

    /** Fonction qui convertie une donnée de type Yes/No/ NON ... en boolean */
    public static function yesNoValueToBoolean(?string $value, mixed $valueIfEmpty=null) :mixed
    {
        if($value===null) { return $valueIfEmpty;}
        $value = strtolower(trim($value));
        // Nécessaire car ne passe pas les switch case
        if(strlen($value) == 0) {
            return $valueIfEmpty;
        }
        return match ($value) {
            'oui', 'o', 'yes', 'y', 'true', 'vrai', '1' => true,
            'non', 'n', 'no', 'false', 'faux', '0' => false,
            default => null,
        };
    }


    public static function textToInt(?string $value, mixed $valueIfFail=null) :mixed
    {
        if($value==null){return $valueIfFail;}
        $str = trim($value);
        $str = str_replace(' ', '', $str);
        $nb = intval($str);
        if(strcmp($str, $nb.'') != 0){return$valueIfFail;}
        return $nb;
    }

    public static function textToDate(?string $value, $valueIfFail=null) : mixed
    {
        if($value==null){return $valueIfFail;}
        $str = trim($value);
        $str = str_replace(' ', '', $str);
        try{
            $date = new DateTime();
            $chaine = explode("/", $value);
            if(sizeof($chaine) != 3){return  $valueIfFail;}
            $date->setDate($chaine[2], $chaine[1], $chaine[0]);
            $date->setTime(0, 0);
            return $date;
        }
        catch (Exception $ex){return  $valueIfFail;}
    }

    public static function textToFloat(?string $value, mixed $valueIfFail=null) : mixed
    {
        if($value==null){return $valueIfFail;}
        $str = trim($value);
//        Pour le cas de nombre a virgule utilisant une virgule au  lieux d'un point
        $str = str_replace(',', '.', $str);
        $str = str_replace(' ', '', $str);
        $nb = floatval($str);
        if(strcmp($str, $nb.'') != 0){return$valueIfFail;}
        return $nb;
    }
}