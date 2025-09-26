<?php


namespace Application\Service\Referentiel;

use Application\Entity\Db\Adresse;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\Source;
use Application\Exceptions\ImportException;
use Application\Service\Misc\CSVService;
use Application\Service\Misc\Traits\CSVServiceAwareTrait;
use Application\Service\Referentiel\Interfaces\AbstractImportEtudiantsService;
use Application\Service\Referentiel\Interfaces\ImportEtudiantsServiceInterface;
use Application\Validator\Import\EtudiantCsvImportValidator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;
use Faker\Core\File;
use UnicaenApp\View\Helper\Messenger;

class CsvEtudiantService extends AbstractImportEtudiantsService implements  ImportEtudiantsServiceInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    use CSVServiceAwareTrait;

    public function getKey(): string
    {
        return Source::CSV;
    }

    protected ?array $csvFileData = null;

    public function getCsvFileData(): ?array
    {
        return $this->csvFileData;
    }

    public function setCsvFileData(?array $csvFileData): void
    {
        $this->csvFileData = $csvFileData;
    }


    /**
     * @throws \Application\Exceptions\ImportException
     */
    protected function assertImportData(array $data) : bool
    {
        try{
            $fileData = $data['fileData'];
            $this->csvService->readCSVFile($fileData);
            $this->getCsvService()->setHeaders(EtudiantCsvImportValidator::getImportHeader());
            $data = $this->getCsvService()->getData();
            $this->setCsvFileData($data);
        }
        catch (Exception $e){
            throw new ImportException("Le fichier données n'est pas valide", previous: $e);
        }
        return true;
    }

    protected function importerEtudiants() : array
    {
        $data = $this->getCsvFileData();
        /** @var Etudiant[] $etudiants */
        $etudiants = [];
        try{
            $cptAdd = 0;
            $cptEdit = 0;
            foreach ($data as $rowData) {
                $etudiant = $this->getEtudiantFromCSVData($rowData);
                $groupe = $this->getGroupeFromCSVData($rowData);
                if(isset($groupe)){
                    $this->addGroupeEtu($groupe, $etudiant);
                }
                if ($etudiant->getId() == null) {
                    $cptAdd++;
                    $this->getObjectManager()->persist($etudiant);
                }
                else{
                    $cptEdit++;
                }
                $etudiants[$etudiant->getNumEtu()] = $etudiant;
            }
            $this->getObjectManager()->flush($etudiants);
            if($cptAdd >0){$this->addLog(sprintf("%s étudiants ajoutés", $cptAdd));}
            if($cptEdit >0){$this->addLog(sprintf("%s étudiants mis à jour", $cptEdit));}
            try{
                $this->getEtudiantService()->updateEtats($etudiants);
            }
            catch (Exception $e){
                $this->addLog("Erreur du calcul des états");
                throw $e;
            }
            $this->setLogType(Messenger::SUCCESS);
        }
        catch (Exception $e){
            $this->setLogType(Messenger::ERROR);
            $this->addLog($e->getMessage());
            throw new ImportException($e->getMessage(), previous: $e);
        }
        $this->setEtudiants($etudiants);
        return $etudiants;
    }

    protected $etuGroupeLinker = [];
    public function addGroupeEtu(Groupe $groupe, Etudiant $etudiant) : static
    {
        $this->addGroupe($groupe);
        $this->etuGroupeLinker[$groupe->getId()][] = $etudiant;
        return $this;
    }

    protected function addEtudiantsInGroupes() : static
    {
        $groupes = $this->getGroupes();
        if(!isset($groupes) || $groupes->isEmpty()){
            $this->addLog("Aucun étudiants à ajouter dans un groupe");
            return $this;
        }
        try {
            foreach ($groupes as $groupe) {
                $etudiants = (($this->etuGroupeLinker[$groupe->getId()])) ?? [];
                $etudiantsCanBeAdd = $this->getGroupeService()->findEtudiantsCanBeAddInGroupe($groupe, $etudiants);
                if(!empty($etudiantsCanBeAdd)) {
                    $this->getGroupeService()->addEtudiants($groupe, $etudiantsCanBeAdd);
                }
                $msg = sprintf("%s étudiants inscrits dans le groupe %s - %s",
                    sizeof($etudiantsCanBeAdd), $groupe->getLibelle(), $groupe->getAnneeUniversitaire()->getLibelle()
                );
                $this->addLog($msg);
                $this->getEtudiantService()->updateEtats($etudiantsCanBeAdd);
            }
        } catch (Exception $e) {
            $this->setLogType(Messenger::ERROR);
            $this->addLog("Erreur lors de l'ajout des étudiants dans les groupes");
            $this->addLog($e->getMessage());
            throw new ImportException($e->getMessage(), previous: $e);
        }
        return $this;
    }

    //TODO : a transformer en CVSEtudiantResult et utilisé l'hydrator ?
    private function getEtudiantFromCSVData(mixed $rowData) : Etudiant
    {
        $numEtu =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_NUM_ETUDIANT, $rowData, ""));
        $nom =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_NOM, $rowData, ""));
        $prenom =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_PRENOM, $rowData, ""));
        $email =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_EMAIL, $rowData, ""));
        $dateNaissance =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_DATE_NAISSANCE, $rowData, ""));
        $dateNaissance = (isset($dateNaissance)) ? CSVService::textToDate($dateNaissance) : null;
        $adresse =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_ADRESSE, $rowData, ""));
        $complement =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_ADRESSE_COMPLEMENT, $rowData, ""));
        $cp =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_CP, $rowData, ""));
        $ville =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_VILLE, $rowData, ""));
        $cedex =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_CEDEX, $rowData, ""));

        $etudiant = $this->getEtudiantService()->findOneBy(['numEtu' => $numEtu]);
        if (!$etudiant) {
            $etudiant = new Etudiant();
            $etudiant->setNumEtu($numEtu);
            $etudiant->setSourceCode($numEtu);
        }
        $etudiant->setNom($nom);
        $etudiant->setPrenom($prenom);
        $etudiant->setEmail($email);
        $etudiant->setDateNaissance($dateNaissance);

        if($etudiant->getAdresse() === null){
            $adresseObject = new Adresse();
            $etudiant->setAdresse($adresseObject);
        }
        $etudiant->getAdresse()->setAdresse($adresse);
        $etudiant->getAdresse()->setComplement($complement);
        $etudiant->getAdresse()->setCodePostal($cp);
        $etudiant->getAdresse()->setVille($ville);
        $etudiant->getAdresse()->setCedex($cedex);
        return $etudiant;
    }

    private function getGroupeFromCSVData(array $rowData) : ?Groupe
    {
        $groupes = $this->getGroupes();
        $codeGroupe =trim($this->getCsvService()->readDataAt(EtudiantCsvImportValidator::HEADER_CODE_GROUPE, $rowData, ""));
        $groupe = $this->getGroupeService()->findOneBy(['code' => $codeGroupe]);
        if(isset($groupe)){
            $groupes->add($groupe);
        }
        return $groupe;

    }

}


