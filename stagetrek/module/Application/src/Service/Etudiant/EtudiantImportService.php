<?php


namespace Application\Service\Etudiant;


use API\Service\Traits\ReferentielEtudiantApiServiceAwareTrait;
use Application\Entity\Db\Adresse;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\TerrainStage;
use Application\Exceptions\ImportException;
use Application\Provider\Roles\RolesProvider;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Misc\CSVService;
use Application\Service\Misc\Traits\CSVServiceAwareTrait;
use Application\Service\Referentiel\Traits\ReferentielServiceAwareTrait;
use Application\Validator\Import\EtudiantCsvImportValidator;
use DateTime;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;
use UnicaenUtilisateur\Entity\Db\RoleInterface;
use UnicaenUtilisateur\Entity\Db\User;
use UnicaenUtilisateur\Service\Role\RoleServiceAwareTrait;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

/**
 * Class EtudiantService
 * @package Application\Service\Etudiant
 */
class EtudiantImportService
    implements
    ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    use UserServiceAwareTrait;
    use RoleServiceAwareTrait;
    //TODO : a revoir car
    /** @return RoleInterface */
    public function getEtudiantRole(): RoleInterface
    {
        return $this->getRoleService()->findByRoleId(RolesProvider::ETUDIANT);
    }

    use ReferentielServiceAwareTrait;
    use EtudiantServiceAwareTrait;

    use ReferentielEtudiantApiServiceAwareTrait;

    /**
     * @param array $fileData
     * @return Etudiant[]
     * @throws \Application\Exceptions\ImportException
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function importEtudiantFromReferentiel(?ReferentielPromo $referentielPromo = null, string $codeAnnee=null): array
    {
        $refSource = $this->getReferentielSourceCode();
        if(!isset($refSource) || $refSource==""){
            Throw new ImportException("L'import depuis un référentiel exterieur n'est pas correctement configuré");
        }
        if (!$referentielPromo) {
            throw new ImportException("La source de l'importation n'as pas été trouvée.");
        }
        $dateSource = $referentielPromo->getSource()->getCode();
        if($refSource != $dateSource){
            Throw new ImportException("L'import depuis le référentiel exterieur n'est pas configuré pour la source de code ".$dateSource);
        }
        $code = $referentielPromo->getCodePromo();
        if(!isset($code) || $code == ""){
            throw new ImportException("Le code de la promotion dans le référentiel n'est pas défini");
        }
        try{
            $apiData = $this->getReferentielEtudiantService()->findByCodePromo($code, $codeAnnee);
        }
        catch (Exception $e){
            $msg = sprintf("Erreur d'accés au référentiel : %s", $e->getMessage());
            throw new ImportException($msg);
        }
        if(!isset($apiData) || $apiData == "" || $apiData == '[]'){
            $msg = "Le référentiel n'as retourné aucun résultat.";
            $msg .= " Vérifiez la disponibilité du service et le code de promo utilisé.";
            throw new ImportException($msg);
        }
        $apiData = json_decode($apiData, true);
        /** @var Etudiant[] $etudiants */
        $etudiants = [];
        foreach ($apiData as $eutData) {
            try {
                $etudiant = $this->getEtudiantFromJSONData($eutData);
                if ($etudiant->getId() == null) {
                    $this->getObjectManager()->persist($etudiant);
                }
                $this->getObjectManager()->flush($etudiant);
                $etudiants[$etudiant->getNumEtu()] = $etudiant;
            }
            catch (Exception $e){
                throw new ImportException($e->getMessage());
            }
        }

        return $etudiants;
    }

    use CSVServiceAwareTrait;

    /**
     * @param array $fileData
     * @return Etudiant[]
     * @throws \Application\Exceptions\ImportException
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function importEtudiantFromCSV(array $fileData): array
    {
        /** @var Etudiant[] $etudiants */
        $etudiants = [];
        $this->getCsvService()->setHeaders(EtudiantCsvImportValidator::getImportHeader());
        $this->csvService->readCSVFile($fileData);
        $data = $this->getCsvService()->getData();
        foreach ($data as $rowData) {
            $etudiant = $this->getEtudiantFromCSVData($rowData);
            if ($etudiant->getId() == null) {
                $this->getObjectManager()->persist($etudiant);
            }
            $this->getObjectManager()->flush($etudiant);
            $etudiants[$etudiant->getNumEtu()] = $etudiant;
        }

        return $etudiants;
    }

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


    protected ?string $referentielSourceCode = null;
    protected array $referentielDataConfig = [];

    public function getReferentielSourceCode(): ?string
    {
        return $this->referentielSourceCode;
    }

    public function setReferentielSourceCode(?string $referentielSourceCode): static
    {
        $this->referentielSourceCode = $referentielSourceCode;
        return $this;
    }


    public function setReferentielDataConfig(array $referentielDataConfig) : static
    {
        $this->referentielDataConfig = $referentielDataConfig;
        return $this;
    }
    public function getReferentielDataConfig() : array
    {
        return  $this->referentielDataConfig;
    }

    protected function getEtudiantFromJSONData(array $eutData) : Etudiant
    {
        $dataConfig = $this->getReferentielDataConfig();

        if(!isset($eutData['numEtu']) ||
            !isset($eutData['nom']) || !
            !isset($eutData['prenom']) ||
            !isset($eutData['email'])
        ){
            throw new Exception("Les données fournisent par le référentiel ne contiennent pas les champs requis");
        }

        $numEtu = $eutData['numEtu'];
        $nom = $eutData['nom'];
        $prenom = $eutData['prenom'];
        $email = $eutData['email'];
        $dateNaissance = ($eutData['dateNaissance']) ?? null;
        if(!isset($numEtu)){
            throw new ImportException("Le numéro de l'étudiant.e n'est pas défini");
        }
        if(!isset($nom)){
            throw new ImportException("Le nom de l'étudiant.e n'est pas défini");
        }
        if(!isset($prenom)){
            throw new ImportException("Le prénom de l'étudiant.e n'est pas défini");
        }
        if(!isset($email)){
            throw new ImportException("L'adresse mail de l'étudiant.e n'est pas défini");
        }

        $etudiant = $this->getEtudiantService()->findOneBy(['numEtu' => $numEtu]);
        if (!$etudiant) {
            $etudiant = new Etudiant();
            $etudiant->setNumEtu($numEtu);
        }
        $etudiant->setNom($nom);
        $etudiant->setPrenom($prenom);
        $etudiant->setEmail($email);
        if(isset($dateNaissance) && $dateNaissance instanceof DateTime) {
            $etudiant->setDateNaissance($dateNaissance);
        }
        if($etudiant->getAdresse() === null){
            $adresse = new Adresse();
            $etudiant->setAdresse($adresse);
        }
        return $etudiant;
    }


    //Fonction qui permet de regarder si certaines entité ont eu des changements depuis le dernier commit
    protected function hasUnitOfWorksChange(): bool
    {
        $unitOfwork = $this->getObjectManager()->getUnitOfWork();
        $unitOfwork->computeChangeSets();
        return
            !empty($unitOfwork->getScheduledEntityInsertions())
            || !empty($unitOfwork->getScheduledEntityUpdates())
            || !empty($unitOfwork->getScheduledCollectionUpdates())
            || !empty($unitOfwork->getScheduledEntityDeletions())
            || !empty($unitOfwork->getScheduledCollectionDeletions());
    }


}