<?php

namespace Application\Validator\Import\Interfaces;

use Application\Exceptions\ImportException;
use Application\Service\Misc\Traits\CSVServiceAwareTrait;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\AbstractValidator;

/**
 * @desc Validateur permettant de déterminer si un fichier d'import est valide
 * @package Application\Validator\Import
 */
abstract class AbstractCsvImportValidator extends AbstractValidator implements ObjectManagerAwareInterface
, CsvImportValidatorInterface
{
    use ProvidesObjectManager;
    use CSVServiceAwareTrait;

    /**
     * Validation
     * @param mixed $value //Ensembles des variables pour la validation
     * @return bool
     */
    public function isValid(mixed $value): bool
    {
        if(!isset($value)){
            $this->error(self::NO_DATA);
            return false;
        }
        if(!is_array($value)){
            $this->error(self::INVALIDE_FILE);
            return false;
        }
        $fileData = $value;
        if(empty($fileData)){
            $this->error(self::NO_DATA);
            return false;
        }
        $this->getCsvService()->setHeaders($this->getImportHeader());
        //Vérification sur la validité du fichier csv
        try {
            $this->csvService->readCSVFile($fileData);
        } catch (ImportException $e) {
            $this->setExceptionMessage($e->getMessage());
            $this->error(self::EXCEPTION);
            return false;
        }
        $data = $this->getCsvService()->getData();
        if(empty($data)){
            $this->error(self::NO_DATA);
            return false;
        }
        $valide = true;
        foreach ($data as $ligne => $rowData){
            try {
                $ligneValide = $this->assertRowValidity($rowData);
                if(!$ligneValide){
                    $valide=false;
                    $msg = sprintf("Ligne %s : Cause indéterminée", $ligne);
                    $this->abstractOptions['messages']['ROW'.$ligne] = $msg;
                }
            } catch (ImportException $e) {
                $msg = sprintf("Ligne %s : %s", $ligne, $e->getMessage());
                $this->abstractOptions['messages']['ROW'.$ligne] = $msg;
                $valide = false;
            }
        }
        return $valide;
    }
    //Fonction qui doit retourner un tableaux contenant le noms des champs attendu dans le fichier d'import
    // ie ['Id','Libelle','Valeur 1', 'Valeur 2']
    /** @return array */
    public static abstract function getImportHeader() : array;
    /**
     * Fonction qui vérifie si une ligne de données est valide
     * usage : utiliser les exceptions pour vérifier si une ligne est invalide pour en donner la cause
     */
    /** @throws \Application\Exceptions\ImportException */
    protected abstract function assertRowValidity() : bool;

    /**************************
     ** Gestion des messages **
     **************************/

    //Formatage des messages d'erreur
    public function getNotAllowedImportMessage() : string
    {
        $messages = $this->getMessages();
        if (empty($messages)) {
            return "L'import des données a échoué";
        }
        $msg = "<b>L'import des données a échoué :</b>";
        $msg .= "<ul class='text-left'>";
        foreach ($messages as $message) {
            $msg .= sprintf("<li>%s</li>", $message);
        }
        $msg .= "</ul>";
        return $msg;
    }

    const NO_DATA = 'NO_DATA';
    const INVALIDE_FILE = 'INVALIDE_FILE';
    const EXCEPTION = 'EXCEPTION';


    /**************************
     ** Gestion des messages **
     **************************/
    /**
     * @var array
     */
    protected $messageTemplates = [
        self::NO_DATA => "Aucune données à importer.",
        self::INVALIDE_FILE => "Le fichier fourni n'est pas valide.",
        self::EXCEPTION => "%exceptionMessage%",
    ];

    /**
     * @var array
     */
    protected $messageVariables = [
        "errorMessage" => "errorMessage",
        "exceptionMessage" => "exceptionMessage",
    ];

    //permet d'afficher un message spécifique sans vraiment passer par un template
    protected ?string $errorMessage = null;
    protected ?string $exceptionMessage = null;

    /**
     * @return ?string
     */
    public function getErrorMessage() : ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param mixed $errorMessage
     */
    public function setErrorMessage(string $errorMessage): static
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * @return ?string
     */
    public function getExceptionMessage() : ?string
    {
        return $this->exceptionMessage;
    }
    /**
     * @param mixed $exceptionMessage
     */
    public function setExceptionMessage(string $exceptionMessage): static
    {
        $this->exceptionMessage = $exceptionMessage;
        return $this;
    }

    /** @var ServiceManager|null $serviceManager */
    private ?ServiceManager $serviceManager=null;

    /** @return ServiceManager|null */
    public function getServiceManager() : ?ServiceManager
    {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager) : static
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
